<?php

declare(strict_types=1);

namespace App\Models\ExtendedDetails;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ExtendedDetailsAlsoBought extends Model
{
    use HasFactory;

    protected $table = 'extended_details_also_bought_carousel';

    protected $fillable = ['extended_details_id'];

    public function extendedDetails()
    {
        return $this->belongsTo(ExtendedDetails::class, 'extended_details_id');
    }
}
