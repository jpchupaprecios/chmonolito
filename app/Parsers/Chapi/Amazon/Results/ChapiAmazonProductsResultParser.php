<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Results;

use DOMDocument;
use DOMXPath;
use Exception;
use Illuminate\Support\Facades\Log;

final class ChapiAmazonProductsResultParser
{
    /**
     * @throws Exception
     */
    public static function parse($res, $vendor): array
    {
        $results = [];

        if (!$res) {
            Log::debug('No HTML content found');
            Log::debug('REQUEST: ' . json_encode($_REQUEST));
            return $results;
        }

        $position = 1;
        foreach ($res as $productElement) {
            $html = self::getElementHtml($productElement);
            if(strpos(strtolower($html), "prescription required") !== false){
                continue;
            }

            $product = ChapiAmazonProductResultParser::parse($productElement, $vendor, $position);

            if (isset($product->price) && $product->price) {
                $results[] = $product;
                $position++;
            }
        }

        return $results;
    }

    private static function getElementHtml($element) {
        $dom = new DOMDocument();
        $dom->appendChild($dom->importNode($element, true));
        return $dom->saveHTML();
    }
}
