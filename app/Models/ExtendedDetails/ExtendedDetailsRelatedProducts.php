<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetailsRelatedProducts extends Model
{
    use HasFactory;

    protected $table = 'extended_details_related_products_carousel';

    protected $fillable = ['extended_details_id'];

    protected $hidden = ['created_at', 'updated_at', 'extended_details_id', 'id'];

    public function ExtendedDetails()
    {
        return $this->belongsTo(ExtendedDetails::class, 'extended_details_id');
    }


    public function relatedProducts()
    {
        return $this->hasMany(ExtendedDetailsRelatedProduct::class, 'extended_details_videos_id');
    }
}
