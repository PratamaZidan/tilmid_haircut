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
        Schema::create('finance_transactions', function (Blueprint $table) {
            $table->id();
            $table->date('date');
            $table->string('type');      // masuk / keluar
            $table->string('category');  // service/operasional/alat/dll
            $table->string('method')->nullable(); // cash/qris/transfer
            $table->integer('amount');
            $table->string('note');
            $table->string('reference_type')->nullable(); // booking / addon / manual
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('capster_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();

            $table->index(['date','type']);
            $table->index(['reference_type','reference_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('finance_transactions');
    }
};
