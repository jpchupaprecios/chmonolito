<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'vendor',
        'image',
        'title',
        'price',
        'type',
        'has_variants',
        'has_combinations',
        'combination_separator',
        'brand',
        'shipping_price',
        'score',
        'rating',
        'description',
        'breadcrumbs_flat',
        'html_description',
        'html_features',
        'html_product_specfics',
        'html_images',
    ];

    // Relación con categories vía pivote
    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    public function thumbnails()
    {
        return $this->hasMany(ProductThumbnail::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

}
