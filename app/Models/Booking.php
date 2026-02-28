<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'code','customer_name','customer_whatsapp',
        'service_code','service_label','service_price',
        'booking_date','booking_time','capster_id','status','notes'
    ];

    public function capster() { return $this->belongsTo(User::class, 'capster_id'); }
    public function addons() { return $this->hasMany(\App\Models\BookingAddon::class); }
}