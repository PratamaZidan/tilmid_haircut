<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CapsterSchedule extends Model
{
    protected $fillable = [
        'capster_id',
        'day_of_week',
        'is_working',
        'start_time',
        'end_time',
        'slot_interval_minutes',
    ];

    public function capster()
    {
        return $this->belongsTo(User::class, 'capster_id');
    }
}