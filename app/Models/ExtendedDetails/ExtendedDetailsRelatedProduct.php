<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetailsRelatedProduct extends Model
{
    use HasFactory;

    protected $table = 'extended_details_related_product_carousel';

    protected $fillable = ['title', 'descriptions', 'rating', 'image','product_id', 'extended_details_related_products_carousel_id'  ];

    protected $hidden = ['created_at', 'updated_at', 'extended_details_id', 'related_products_id', 'id'];

    public function relatedProduct()
    {
        return $this->belongsTo(ExtendedDetailsRelatedProducts::class, 'extended_details_id');
    }
}
