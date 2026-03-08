<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\BusinessHour;

class BusinessHourSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            0 => ['is_open' => true, 'open_time' => '13:00:00', 'close_time' => '22:00:00'],
            1 => ['is_open' => true, 'open_time' => '13:00:00', 'close_time' => '22:00:00'],
            2 => ['is_open' => true, 'open_time' => '13:00:00', 'close_time' => '22:00:00'],
            3 => ['is_open' => true, 'open_time' => '13:00:00', 'close_time' => '22:00:00'],
            4 => ['is_open' => true, 'open_time' => '13:00:00', 'close_time' => '22:00:00'],
            5 => ['is_open' => true, 'open_time' => '13:00:00', 'close_time' => '22:00:00'],
            6 => ['is_open' => true, 'open_time' => '13:00:00', 'close_time' => '22:00:00'],
        ];

        foreach ($defaults as $day => $row) {
            BusinessHour::updateOrCreate(
                ['day_of_week' => $day],
                $row
            );
        }
    }
}