<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kedai extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_id',
        'product_id',
        'address',
        'phone_number',
    ];
    public function user()
    {
        return $this->hasOne(User::class , 'id', 'user_id');
    }
    
    public function products(){
        return $this->hasMany(Product::class, 'id', 'product_id');
    }

}
