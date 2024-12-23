<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Helpers\FixEncoding;

final class VariantOption extends Model
{
	use HasFactory;

	protected $fillable = ['title', 'product_id', 'image', 'selected', 'available', 'variant_id'];

	// region properties

	public function variant(): BelongsTo
	{
		return $this->belongsTo(Variant::class);
	}

	public function getData(): array
	{
		return [
			'title' => FixEncoding::fix($this->getAttribute('title')),
			'product_id' => FixEncoding::fix($this->getAttribute('product_id')),
			'image' => FixEncoding::fix($this->getAttribute('image')),
			'selected' => FixEncoding::fix($this->getAttribute('selected')),
			'available' => FixEncoding::fix($this->getAttribute('available')),
		];
	}


}
