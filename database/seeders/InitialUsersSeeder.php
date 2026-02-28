<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class InitialUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['username' => 'admin_tilmid'],
            [
                'name' => 'Admin Tilmid',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => 'raka'],
            [
                'name' => 'Raka Gymnastiar',
                'password' => Hash::make('capster123'),
                'role' => 'capster',
                'phone' => '0851xxxxxxx',
                'status' => 'aktif',
            ]
        );

        User::updateOrCreate(
            ['username' => 'putra'],
            [
                'name' => 'Putra',
                'password' => Hash::make('capster123'),
                'role' => 'capster',
                'phone' => '0851xxxxxxx',
                'status' => 'aktif',
            ]
        );
    }
}
