<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Thumbnail extends Model
{
	use HasFactory;

    protected $table = 'thumbnails';

	protected $fillable = ['link', 'product_details_id'];

	// region properties

	/**
	 * @type string
	 */
	protected $link;

    public function product()
    {
        return $this->belongsTo(ProductDetails::class, 'product_details_id');
    }

	public function getData(): array
	{
		return [
			'link' => $this->getAttribute('link'),
		];
	}
}
