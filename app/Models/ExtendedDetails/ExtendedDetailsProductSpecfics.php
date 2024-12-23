<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetailsProductSpecfics extends Model
{
    use HasFactory;

    protected $table = 'extended_details_product_specifics';

    protected $fillable = ['title', 'data'];

    public function extendedDetails()
    {
        return $this->belongsTo(ExtendedDetails::class, 'extended_details_id');
    }
}
