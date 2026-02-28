<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinanceTransaction extends Model
{
    protected $fillable = [
        'date',
        'type',       // masuk / keluar
        'category',   // service / operasional / alat / dll
        'method',     // cash / qris / transfer (nullable)
        'amount',
        'note',
        'capster_id', // nullable
        'created_by',
    ];

    public function capster()
    {
        return $this->belongsTo(User::class, 'capster_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}