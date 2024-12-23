<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Homedepot\Results;

use App\Helpers\Getter;
use Exception;
use DOMDocument;
use DOMXPath;

final class ChapiHomedepotProductsResultParser
{
    private static $jsonData;

    /**
     * @throws Exception
     */
    public static function parse($xpath, $vendor): array
    {
        $results = [];

        self::$jsonData = null;

        // Encontrar y procesar todos los elementos de script
        $scriptsDom = $xpath->query('//script');
        foreach ($scriptsDom as $script) {
            $scriptContent = $script->textContent;
            $jsonData = self::isJson($scriptContent);
            if ($jsonData) {
                self::$jsonData = Getter::getValueByKeys($jsonData, ['mainEntity', 'offers', 'itemOffered']);
                break;
            }
        }

        // Encontrar el contenedor de los productos
        $itemsWrapper = $xpath->query('//*[@id="browse-search-pods-1"]')->item(0);
        if (!$itemsWrapper) {
            return [];
        }

        // Iterar sobre los elementos hijos del contenedor de productos
        foreach ($itemsWrapper->childNodes as $item) {
            if ($item->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $product = ChapiHomedepotProductResultParser::parse($item, $vendor, count($results) + 1, self::$jsonData);

            if (!$product->getAttribute('product_id') || !$product->getAttribute('price') || !$product->getAttribute('title')) {
                continue;
            }

            $results[] = $product;
        }

        return $results;
    }

    private static function isJson($script)
    {
        $json = json_decode($script);
        if ($json && json_last_error() === JSON_ERROR_NONE) {
            if (isset($json->mainEntity)) {
                return $json;
            }
        }
        return false;
    }
}
