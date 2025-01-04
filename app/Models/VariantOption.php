<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantOption extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_variant_id',
        'title',
        'option_product_id',
        'image',
        'selected',
        'available',
    ];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
