<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Product\Variants;

final class ChapiEbayVariantDimensionParser
{
	/**
	 * @var mixed
	 */
	private $dom;

	public function __construct($dom = null)
	{
		if ($dom) {
			$this->dom = $dom;
		}
	}
	public function parse($currentAsin): array
	{
		if (!$this->dom) {
			return [];
		}

		return [];
	}
}
