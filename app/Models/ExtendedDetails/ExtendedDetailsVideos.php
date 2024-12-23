<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use App\Models\Product\ProductDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetailsVideos extends Model
{
    use HasFactory;

    protected $table = 'extended_details_videos';

    protected $fillable = ['extended_details_id'];

    protected $hidden = ['created_at', 'updated_at', 'product_details_id', 'id'];

    public function extendedDetails()
    {
        return $this->belongsTo(ExtendedDetails::class, 'extended_details_id');
    }

    public function videos()
    {
        return $this->hasMany(ExtendedDetailsVideo::class, 'extended_details_videos_id');
    }
}
