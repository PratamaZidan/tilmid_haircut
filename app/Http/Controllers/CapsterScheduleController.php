<?php

namespace App\Http\Controllers;

use App\Models\CapsterSchedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CapsterScheduleController extends Controller
{
    public function edit()
    {
        $user = Auth::user();

        $schedules = CapsterSchedule::where('capster_id', $user->id)
            ->orderBy('day_of_week')
            ->get()
            ->keyBy('day_of_week');

        return view('capster.schedule', compact('user', 'schedules'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'schedules' => 'required|array',
            'schedules.*.is_working' => 'nullable',
            'schedules.*.start_time' => 'nullable',
            'schedules.*.end_time' => 'nullable',
            'schedules.*.slot_interval_minutes' => 'nullable|integer|min:30|max:180',
        ]);

        foreach ($data['schedules'] as $day => $row) {
            CapsterSchedule::updateOrCreate(
                [
                    'capster_id' => $user->id,
                    'day_of_week' => $day,
                ],
                [
                    'is_working' => isset($row['is_working']),
                    'start_time' => $row['start_time'] ?: null,
                    'end_time' => $row['end_time'] ?: null,
                    'slot_interval_minutes' => $row['slot_interval_minutes'] ?: 60,
                ]
            );
        }

        return back()->with('ok_schedule', 'Jadwal kerja berhasil disimpan.');
    }
}