<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserLocation extends Model
{
    use HasFactory;
    protected $fillable = [
        'user_id',
        'address',
        'customer_name',
        'phone_number',
        'longitude',
        'latitude',
        'address_type',
    ];
}
