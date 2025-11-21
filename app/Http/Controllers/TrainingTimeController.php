<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTrainingTimesRequest;
use App\Jobs\CreateTrainingJob;
use App\Models\Training;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;

class TrainingTimeController extends Controller
{
    public function __invoke(StoreTrainingTimesRequest $request): JsonResponse
    {
        $trainingTimes = collect($request->validated('training_times'));
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addWeeks(4);

        $createdToday = 0;
        $scheduledAsync = 0;
        $trainingToday = null;

        foreach ($trainingTimes as $time) {
            $date = $startDate->copy();

            while ($date->dayOfWeekIso !== $time['weekday']) {
                $date = $date->addDay();
            }

            while ($date->lt($endDate)) {
                $scheduledAt = $date->copy()->setTime($time['hours'], $time['minutes']);

                if ($date->isSameDay($startDate)) {
                    $training = Training::firstOrCreate(['scheduled_at' => $scheduledAt]);
                    $createdToday += (int) $training->wasRecentlyCreated;
                    $trainingToday = $training;
                } else {
                    CreateTrainingJob::dispatch($scheduledAt);
                    $scheduledAsync++;
                }

                $date = $date->addWeek();
            }
        }

        return response()->json([
            'created_today' => $createdToday,
            'scheduled_async' => $scheduledAsync,
            'training' => $trainingToday ? [
                'id' => $trainingToday->id,
                'scheduled_at' => $trainingToday->scheduled_at->toISOString(),
            ] : null,
        ]);
    }
}
