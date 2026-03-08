<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('capster_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('capster_id')->constrained('users')->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // 0-6
            $table->boolean('is_working')->default(true);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->unsignedInteger('slot_interval_minutes')->default(60);
            $table->timestamps();

            $table->unique(['capster_id', 'day_of_week']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('capster_schedules');
    }
};