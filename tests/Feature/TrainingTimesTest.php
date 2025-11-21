<?php

namespace Tests\Feature;

use App\Jobs\CreateTrainingJob;
use App\Models\Training;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class TrainingTimesTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_creates_training_for_today_and_dispatches_future_dates(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 2, 3, 9, 0, 0, 'UTC')); // Monday
        Queue::fake();

        $response = $this->postJson('/training-times', [
            'training_times' => [
                ['hours' => 16, 'minutes' => 30, 'weekday' => 1],
            ],
        ]);

        $response->assertOk()
            ->assertJson([
                'created_today' => 1,
                'scheduled_async' => 3,
            ]);

        $this->assertDatabaseCount('trainings', 1);
        $this->assertTrue(
            Carbon::parse($response->json('training.scheduled_at'))
                ->equalTo(Carbon::parse('2025-02-03 16:30:00', 'UTC'))
        );

        Queue::assertPushed(CreateTrainingJob::class, 3);

        foreach ([
            Carbon::parse('2025-02-10 16:30:00', 'UTC'),
            Carbon::parse('2025-02-17 16:30:00', 'UTC'),
            Carbon::parse('2025-02-24 16:30:00', 'UTC'),
        ] as $expectedDate) {
            Queue::assertPushed(
                CreateTrainingJob::class,
                fn (CreateTrainingJob $job): bool => $job->scheduledAt->eq($expectedDate)
            );
        }
    }

    public function test_it_rejects_duplicate_weekdays(): void
    {
        Queue::fake();

        $response = $this->postJson('/training-times', [
            'training_times' => [
                ['hours' => 8, 'minutes' => 0, 'weekday' => 2],
                ['hours' => 10, 'minutes' => 30, 'weekday' => 2],
            ],
        ]);

        $response->assertStatus(422);
        Queue::assertNothingPushed();
        $this->assertDatabaseCount('trainings', 0);
    }

    public function test_it_returns_null_when_no_training_today(): void
    {
        Carbon::setTestNow(Carbon::create(2025, 2, 3, 9, 0, 0, 'UTC')); // Monday
        Queue::fake();

        $response = $this->postJson('/training-times', [
            'training_times' => [
                ['hours' => 9, 'minutes' => 45, 'weekday' => 3], // Wednesday
            ],
        ]);

        $response->assertOk()
            ->assertJson([
                'created_today' => 0,
                'scheduled_async' => 4,
                'training' => null,
            ]);

        $this->assertDatabaseCount('trainings', 0);
        Queue::assertPushed(CreateTrainingJob::class, 4);
    }
}
