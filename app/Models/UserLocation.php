<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    public function user(){
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
