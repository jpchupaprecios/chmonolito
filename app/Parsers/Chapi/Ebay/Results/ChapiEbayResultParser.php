<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Results;

use App\Models\Search\Pagination\Pagination;
use App\Models\Search\Result;
use App\Parsers\Chapi\Ebay\Results\Pagination\ChapiEbayPaginationParser;
use App\Parsers\Chapi\Ebay\Results\Refinements\ChapiEbayRefinementsParser;

use Exception;

final class ChapiEbayResultParser
{
	/**
	 * @var mixed
	 */
	private $dom;
	/**
	 * @var mixed
	 */
	private $result;
	/**
	 * @var array
	 */
	private $products;

	public array $refinements = [];
	public Pagination $pagination;

	public function __construct() {}

	/**
	 * @throws Exception
	 */
	public function parse($result, $vendor, $query, $page): Result
	{
		$this->results = $result;
		$this->products = [];

		$result = new Result();
		$result->setAttribute('vendor', $vendor);
		$result->setAttribute('query', $query);

		$this->products = ChapiEbayProductsResultParser::parse($this->results, $vendor);
		$refinements = ChapiEbayRefinementsParser::parse($this->results, $vendor, $query);
		$pagination = ChapiEbayPaginationParser::parse($this->results, $page);

		if ($refinements) {
			$this->refinements = $refinements;
		}

		if ($pagination) {
			$this->pagination = $pagination;
		} else {
			$this->pagination = new Pagination();
		}

		$result->setAttribute('totalProducts', count($this->products));
		$result->setRelation('products', $this->products);
		$result->setRelation('pagination', $this->pagination);
		$result->setRelation('refinements', $this->refinements);

		return $result;
	}

	private function getTranslateRefinements(): array
	{
		$allTextsToTranslate = [];

		foreach ($this->products as $product) {
			$allTextsToTranslate[] = $product->getAttribute('title');
		}

		foreach ($this->refinements as $refinement) {
			$allTextsToTranslate[] = $refinement->getAttribute('title');
			$links = $refinement->getRelation('links');

			if ($links) {
				foreach ($links as $link) {
					$allTextsToTranslate[] = $link->getAttribute('title');
				}
			}
		}

		$translatedTexts = GoogleTranslate::multiTranslates('en', 'es', $allTextsToTranslate);

		if (count($translatedTexts) !== count($allTextsToTranslate)) {
			return [];
		}

		return $translatedTexts;
	}

	private function translateRefinements($translatedTexts): void
	{
		if (!empty($translatedTexts)) {
			foreach ($this->products as $index => $product) {
				$product->setAttribute('title', $translatedTexts[$index]);
			}

			$currentIndex = count($this->products);
			foreach ($this->refinements as $refinement) {
				$refinement->setAttribute('title', $translatedTexts[$currentIndex++]);
				$links = $refinement->getRelation('links');
				if ($links) {
					foreach ($links as $link) {
						$link->setAttribute('title', $translatedTexts[$currentIndex++]);
					}
				}
			}
		}
	}
}
