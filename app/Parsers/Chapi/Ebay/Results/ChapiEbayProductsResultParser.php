<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Results;

use Exception;
use DOMDocument;
use DOMXPath;

final class ChapiEbayProductsResultParser
{

    /**
     * @throws Exception
     */
    public static function parse($xpath, $vendor): array
    {
        $results = [];

        // Encontrar todos los elementos con la clase 's-item__wrapper clearfix'
        $rawItems = $xpath->query('//div[contains(@class, "s-item__wrapper clearfix")]');

        if ($rawItems->length > 0) {
            // Si hay elementos encontrados, omitir el primero
            $rawItems = iterator_to_array($rawItems);
            array_shift($rawItems);
        }

        foreach ($rawItems as $item) {
            $product = ChapiEbayProductResultParser::parse($item, $vendor, count($results) + 1);

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
