<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'users_id', 'kurir_id', 'address', 'payment', 'total_price', 'shipping_price', 'status', 'user_location_id', 'rating', 'note'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'users_id', 'id');
    }
    public function kurir()
    {
        return $this->belongsTo(User::class, 'kurir_id', 'id');
    }

    public function items()
    {
        return $this->hasMany(TransactionItem::class, 'transactions_id', 'id');
    }
    public function user_location()
    {
        return $this->belongsTo(UserLocation::class, 'user_location_id', 'id');
    }
}
