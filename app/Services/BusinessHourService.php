<?php

namespace App\Services;

use App\Models\BusinessHour;
use Carbon\Carbon;

class BusinessHourService
{
    public function getWeeklyHours()
    {
        return BusinessHour::orderBy('day_of_week')->get();
    }

    public function isOpenNow(): bool
    {
        $now = Carbon::now('Asia/Jakarta');
        $day = $now->dayOfWeek;

        $row = BusinessHour::where('day_of_week', $day)->first();

        if (!$row || !$row->is_open || !$row->open_time || !$row->close_time) {
            return false;
        }

        $open = Carbon::parse($now->toDateString() . ' ' . $row->open_time, 'Asia/Jakarta');
        $close = Carbon::parse($now->toDateString() . ' ' . $row->close_time, 'Asia/Jakarta');

        return $now->gte($open) && $now->lt($close);
    }

    public function getTodayHours(): ?BusinessHour
    {
        return BusinessHour::where('day_of_week', Carbon::now('Asia/Jakarta')->dayOfWeek)->first();
    }
}