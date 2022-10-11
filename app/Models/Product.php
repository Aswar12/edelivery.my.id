<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = [
        'name', 'description', 'price', 'categories_id', 'tags', 'kedai_id'
    ];    

    public function galleries()
    {
        return $this->hasMany(ProductGallery::class, 'products_id', 'id');
    }
    
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'categories_id', 'id');
    }

    public function kedai()
    {
        return $this->belongsTo(Kedai::class, 'kedai_id', 'id');
    }
}
