<?php

declare(strict_types=1);

namespace App\Models\Search;

use App\Helpers\FixEncoding;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class ProductResult extends Model
{
	use HasFactory;

	protected $fillable = ['product_id', 'title', 'price', 'score', 'rating', 'image', 'description', ];

    public function getData(): ProductResult
    {
        $result = new ProductResult();

        $result->product_id = $this->getAttribute('product_id');
        $result->title = FixEncoding::fix($this->getAttribute('title') ?? '');
        $result->description = FixEncoding::fix($this->getAttribute('description') ?? '');
        $result->price = $this->getAttribute('price');
        $result->score = $this->getAttribute('score');
        $result->rating = $this->getAttribute('rating');
        $result->image = $this->getAttribute('image');

        return $result;
    }
}
