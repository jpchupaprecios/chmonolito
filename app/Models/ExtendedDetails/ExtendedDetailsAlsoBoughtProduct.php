<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetailsAlsoBoughtProduct extends Model
{
    use HasFactory;

    protected $table = 'extended_details_also_bought_product_carousel';

    protected $fillable = ['title', 'descriptions', 'rating', 'image', 'extended_details_related_products_carousel_id'  ];
    public function extendedDetails()
    {
        return $this->belongsTo(ExtendedDetails::class, 'extended_details_id');
    }
}
