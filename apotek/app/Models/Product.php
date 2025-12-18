<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'stock',
        'image_url',
        'category_id',
        'is_active'
    ];

    protected static function booted()
{
    static::creating(function ($product) {
        if (empty($product->slug)) {
            $slug = Str::slug($product->name);
            $count = static::where('slug', 'LIKE', "{$slug}%")->count();
            $product->slug = $count ? "{$slug}-{$count}" : $slug;
        }
    });
}


    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}
