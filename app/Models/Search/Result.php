<?php

declare(strict_types=1);

namespace App\Models\Search;

use App\Models\Search\Pagination\Pagination;
use App\Models\Search\Refinements\Refinement;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\DB;

final class Result extends Model
{
	use HasFactory;

	protected $fillable = ['query', 'vendor', 'page', 'totalProducts'];

	public function __construct(array $attributes = [])
	{
		parent::__construct($attributes);
	}

	public function products(): HasMany
	{
		return $this->hasMany(ProductResult::class);
	}

	public function pagination(): HasOne
	{
		return $this->HasOne(Pagination::class);
	}

	public function refinements(): HasMany
	{
		return $this->hasMany(Refinement::class);
	}

	public function getData(): array
	{
		$refinements = [];

		if ($this->refinements() && $this->refinements()->get() && count($this->refinements()->get())) {
			foreach ($this->refinements()->get() as $refinement) {
				$refinements[] = $refinement->getData();
			}
		} elseif ($this->getRelation('refinements')) {
			foreach ($this->getRelation('refinements') as $refinement) {
				$refinements[] = $refinement->getData();
			}
		}

		$paginationModel = $this->pagination()->with('links')->first();
		$pagination = [];

		if ($paginationModel) {
			$pagination = $paginationModel->getData();
		} elseif ($this->getRelation('pagination')) {
			$pagination = $this->getRelation('pagination');
			$pagination = $pagination->getData();
		}

        $products = $this->getRelation('products');
        $productsData = [];
        foreach ($products as $product){
            $productsData[] = $product->getData();
        }

		return [
			'products' => $productsData,
			'pagination' => $pagination,
			'refinements' => $refinements,
			'totalProducts' => $this->getAttribute('totalProducts'),
			'vendor' => $this->getAttribute('vendor'),
			'engine' => $this->getAttribute('engine'),
		];
	}

	public function store()
	{
		DB::transaction(function () {
			$this->save();

			foreach ($this->getRelation('products') as $product) {
				$this->products()->save($product);
			}

			$pagination = $this->getRelation('pagination');
			$current = $pagination->getAttribute('current');
			if ($current) {
				$this->pagination()->save($pagination);

				foreach ($pagination->getRelation('links') as $link) {
					$link->setAttribute('pagination_id', $pagination->getAttribute('id'));
					$pagination->links()->save($link);
				}
			}

			$refinements = $this->getRelation('refinements');
			foreach ($refinements as $refinement) {
				$title = $refinement->getAttribute('title');
				if ($title) {
					$this->refinements()->save($refinement);

					$links = $refinement->getRelation('links');
					foreach ($links as $link) {
						$refinement->links()->save($link);
					}
				}
			}
		});
	}
}
