<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Product\Offers;

use App\Models\Product\Offer;

final class ChapiEbayOfferParser
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
	public function parse($offerData): Offer
	{
		$offer = new Offer();
		$offer->setPrice($offerData->price);
		$offer->setAvailability($offerData->availability);
		$offer->setItemCondition($offerData->itemCondition);

		return $offer;
	}
}
