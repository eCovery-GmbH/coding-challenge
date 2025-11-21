<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;


class Training extends Model
{

    protected $fillable = [
        'user_id',
        'hours',
        'minutes',
        'weekdays',
        'scheduled_async',
    ];

 
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    
    protected function casts(): array
    {
        $today = Carbon::now()->format('Y-m-d');
        $createdAt = Carbon::parse($this->created_at)->format('Y-m-d');

        if ($createdAt === $today) {
            $casts = [
            'created_today' => Carbon::parse([$this->created_at])->format('Y-m-d\TH:i:s'),
            'scheduled_async' => 'integer',
            'training' => [
                'id' => $this->id,
                'scheduled_at' => Carbon::parse($this->created_at)->format('Y-m-d\TH:i:s')
            ]
        ];
        } 
        else {
            $casts = [
            'created_today' => 0,
            'scheduled_async' => 'integer',
            'training' => null
            ];
        }

        return $casts;
    }
}
