<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    protected $fillable = [
        'code','name','description','price','category','sort_order','is_active','is_public'
    ];
}
