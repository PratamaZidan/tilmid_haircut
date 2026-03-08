<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\BusinessHour;
use App\Models\CapsterSchedule;
use Carbon\Carbon;

class BookingAvailabilityService
{
    public function getAvailableSlots(string $date, int $capsterId): array
    {
        $targetDate = Carbon::parse($date);
        $dayOfWeek = $targetDate->dayOfWeek;

        $businessHour = BusinessHour::where('day_of_week', $dayOfWeek)->first();
        if (!$businessHour || !$businessHour->is_open || !$businessHour->open_time || !$businessHour->close_time) {
            return [
                'date' => $date,
                'is_shop_open' => false,
                'is_capster_working' => false,
                'all_slots' => [],
                'booked_slots' => [],
                'available_slots' => [],
                'is_fully_booked' => true,
            ];
        }

        $schedule = CapsterSchedule::where('capster_id', $capsterId)
            ->where('day_of_week', $dayOfWeek)
            ->first();

        if (!$schedule || !$schedule->is_working || !$schedule->start_time || !$schedule->end_time) {
            return [
                'date' => $date,
                'is_shop_open' => true,
                'is_capster_working' => false,
                'all_slots' => [],
                'booked_slots' => [],
                'available_slots' => [],
                'is_fully_booked' => true,
            ];
        }

        $start = Carbon::parse($date . ' ' . max($businessHour->open_time, $schedule->start_time));
        $end = Carbon::parse($date . ' ' . min($businessHour->close_time, $schedule->end_time));

        $interval = (int) ($schedule->slot_interval_minutes ?: 60);

        $allSlots = [];
        $cursor = $start->copy();

        while ($cursor < $end) {
            $allSlots[] = $cursor->format('H:i');
            $cursor->addMinutes($interval);
        }

        $bookedSlots = Booking::where('capster_id', $capsterId)
            ->whereDate('booking_date', $date)
            ->pluck('booking_time')
            ->map(fn($t) => Carbon::parse($t)->format('H:i'))
            ->values()
            ->all();

        $availableSlots = array_values(array_diff($allSlots, $bookedSlots));

        return [
            'date' => $date,
            'is_shop_open' => true,
            'is_capster_working' => true,
            'all_slots' => $allSlots,
            'booked_slots' => $bookedSlots,
            'available_slots' => $availableSlots,
            'is_fully_booked' => count($availableSlots) === 0,
        ];
    }

    public function getFullyBookedDates(int $capsterId, string $month): array
    {
        $start = Carbon::parse($month . '-01')->startOfMonth();
        $end = $start->copy()->endOfMonth();

        $dates = [];
        $cursor = $start->copy();

        while ($cursor <= $end) {
            $result = $this->getAvailableSlots($cursor->toDateString(), $capsterId);

            if ($result['is_fully_booked']) {
                $dates[] = $cursor->toDateString();
            }

            $cursor->addDay();
        }

        return $dates;
    }
}