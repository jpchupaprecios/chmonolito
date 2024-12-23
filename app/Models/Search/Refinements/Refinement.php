<?php

declare(strict_types=1);

namespace App\Models\Search\Refinements;

use App\Models\Product\ProductDetails;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class Refinement extends Model
{
	use HasFactory;

	protected $table = 'refinement';

	protected $fillable = ['title'];

	public function links(): \Illuminate\Database\Eloquent\Relations\HasMany
	{
		return $this->hasMany(RefinementLinks::class);
	}

	public function product(): \Illuminate\Database\Eloquent\Relations\BelongsTo
	{
		return $this->belongsTo(ProductDetails::class);
	}

	public function getData(): array
	{
		$links = $this->links()->get();
		if ($links && count($links)) {
			foreach ($links as $link) {
                if (is_resource($link->getAttribute('link'))) {
                    $l = stream_get_contents($link->getAttribute('link'));
                    $link->setAttribute('link', $l);
                }
			}
		} else {
			$links = $this->getRelation('links');
		}


		return [
			'title' => $this->getAttribute('title'),
			'links' => $links,
		];
	}
}
