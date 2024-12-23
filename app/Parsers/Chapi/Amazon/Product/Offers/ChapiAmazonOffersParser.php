<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Product\Offers;

final class ChapiAmazonOffersParser
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

		$this->offerParser = new ChapiEbayOfferParser($dom);
	}
	public function parse($offersData)
	{
		if (!$this->dom) {
			return [];
		}
		$offers = [];
		if (isset($offersData->offers) && is_array($offersData->offers)) {
			foreach ($offersData->offers as $offerData) {
				$offer = $this->offerParser->parse($offerData);
				$offers[] = $offer;
			}
		}

		return $offers;
	}
}
