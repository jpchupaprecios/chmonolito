<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Results;

use Exception;

final class ChapiWalmartProductsResultParser
{

	/**
	 * @throws Exception
	 */
	public static function parse($scriptData, $vendor): array
	{
		$results = [];
        $rawItems = [];

        if($scriptData){
            $rawItems = $scriptData->props->pageProps->initialData->searchResult->itemStacks[0]->items;
            if (!empty($rawItems)) {
                //$rawItems = array_slice($rawItems, 1);
            }
        }

		foreach ($rawItems as $item) {
			$product = ChapiWalmartProductResultParser::parse($item, $vendor, count($results) + 1);

			if (!$product->getAttribute('product_id') || !$product->getAttribute('price') || !$product->getAttribute(
				'title'
			)) {
				continue;
			}

			$results[] = $product;
		}

		return $results;
	}
}
