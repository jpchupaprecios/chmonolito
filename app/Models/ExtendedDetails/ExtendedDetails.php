<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use App\Models\Product\ProductDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetails extends Model
{
	use HasFactory;

    protected $fillable = ['product_id', 'vendor', 'html_description', 'html_features', 'html_images', 'html_product_specfics', 'brand', 'product_details_id'];

    protected $hidden = ['created_at', 'updated_at', 'id'];

    protected $table = 'extended_details';

    public function videos()
    {
        return $this->hasOne(ExtendedDetailsVideos::class, 'extended_details_id');
    }
    public function alsoBought()
    {
        return $this->hasOne(ExtendedDetailsAlsoBought::class, 'extended_details_id');
    }

    public function relatedProducts()
    {
        return $this->hasOne(ExtendedDetailsRelatedProducts::class, 'extended_details_id');
    }

    public function product()
    {
        return $this->belongsTo(ProductDetails::class, 'product_details_id');
    }

}
