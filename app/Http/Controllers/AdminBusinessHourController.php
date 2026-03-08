<?php

namespace App\Http\Controllers;

use App\Models\BusinessHour;
use Illuminate\Http\Request;

class AdminBusinessHourController extends Controller
{
     public function index()
    {
        $hours = BusinessHour::orderBy('day_of_week')->get()->keyBy('day_of_week');

        return view('admin.business-hours', compact('hours'));
    }
    
    public function update(Request $request)
    {
        $data = $request->validate([
            'hours' => 'required|array',
            'hours.*.is_open' => 'nullable',
            'hours.*.open_time' => 'nullable',
            'hours.*.close_time' => 'nullable',
        ]);

        foreach ($data['hours'] as $day => $row) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $day],
                [
                    'is_open' => isset($row['is_open']),
                    'open_time' => $row['open_time'] ?: null,
                    'close_time' => $row['close_time'] ?: null,
                ]
            );
        }

        return back()->with('ok_business_hours', 'Jam operasional berhasil disimpan.');
    }
}