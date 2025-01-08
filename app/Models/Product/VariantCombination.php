<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

final class VariantCombination extends Model
{
	use HasFactory;

	protected $fillable = [
        'variant_key',
        'variant_sku',
        'product_details_id',
    ];

	protected $table = 'variant_combinations';

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductDetails::class, 'product_details_id');
    }

    public function getData(): array
    {
        return [
            'variant_key' => $this->getAttribute('variant_key'),
            'variant_sku' => $this->getAttribute('variant_sku'),
        ];
    }

    public function store(): void
    {
        $this->save();
    }
}
