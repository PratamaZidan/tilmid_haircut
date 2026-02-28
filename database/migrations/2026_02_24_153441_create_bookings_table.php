<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_whatsapp');
            $table->string('service_code');      // reguler, premium, semirhitam, dll
            $table->string('service_label');     // "Premium"
            $table->integer('service_price');    // 30000
            $table->date('booking_date');
            $table->time('booking_time');
            $table->foreignId('capster_id')->constrained('users');
            $table->string('status')->default('pending'); // pending/confirmed/done/cancelled
            $table->text('notes')->nullable();
            $table->string('code')->unique();    // kode booking untuk success page
            $table->timestamps();

            $table->index(['booking_date', 'capster_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
