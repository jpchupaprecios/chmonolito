<?php

declare(strict_types=1);

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\FixEncoding;
final class Category extends Model
{
	use HasFactory;

	protected $table = 'categories';

	protected $fillable = ['link', 'name', ];

	// region properties

	/**
	 * @type string
	 */
	protected $name;

	/**
	 * @type string
	 */
	protected $link;

	public function product()
	{
		return $this->belongsTo(ProductDetails::class);
	}

	public function getData(): array
	{
		return [
			'link' => FixEncoding::fix($this->getAttribute('link')),
			'name' => FixEncoding::fix($this->getAttribute('name')),
		];
	}
}
