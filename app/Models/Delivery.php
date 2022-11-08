<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    use HasFactory;
    protected $fillable = [
        'transactions_id',
        'kurir_id',
    ];

    public function transaction()
    {
        return $this->hasOne(Transaction::class, 'transactions_id', 'id');
    }

    public function kurir()
    {
        return $this->hasOne(User::class, 'kurir_id', 'id');
    }
}
