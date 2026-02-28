<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // kalau masih ada baris factory bawaan, boleh dihapus/comment
        // User::factory()->create([...]);

        $this->call([InitialUsersSeeder::class,]);
        $this->call([ServicesSeeder::class,]);
    }
}
