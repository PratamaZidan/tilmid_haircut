<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingAddon extends Model
{
    protected $fillable = [
        'booking_id',
        'addon_type',
        'addon_label',
        'amount',
        'note',
    ];

    public function booking() { return $this->belongsTo(\App\Models\Booking::class); }
}
