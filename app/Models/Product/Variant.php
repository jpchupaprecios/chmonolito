<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Helpers\FixEncoding;
final class Variant extends Model
{
	use HasFactory;

	protected $table = 'variants';

	protected $fillable = ['type', 'title', 'product_details_id', ];

	// region properties

	public function product(): BelongsTo
	{
		return $this->belongsTo(ProductDetails::class);
	}
	public function options(): HasMany
	{
		return $this->hasMany(VariantOption::class);
	}

	// endregion properties

	public function getData(): array
	{
		$options = [];
		foreach ($this->options()->get() as $option) {
			$options[] = $option->getData();
		}

		return [
			'title' => FixEncoding::fix($this->getAttribute('title')),
			'type' => FixEncoding::fix($this->getAttribute('type')),
			'options' => $options,
		];
	}

}
