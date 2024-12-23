<?php

namespace App\Services\Chapi\Ebay;

use App\Models\Product\ProductDetails;
use App\Parsers\Chapi\Amazon\Product\ChapiAmazonProductDetailParser;
use App\Services\Chapi\Amazon\ChapiAmazonWebContentService;
use App\Services\Chapi\Amazon\Exception;
use App\Services\CookieService;
use App\Services\Interfaces\ExtendedDetailsServiceInterface;
use Illuminate\Http\Request;

class ExtendedDetailsEbayService implements ExtendedDetailsServiceInterface
{
    public function __construct() {}

    public function getData(string $productId): array
    {
        $suggestions = $this->fetchSuggestionsFromAmazon($productId);

        return ChapiAmazonAutocompleteParser::getSugs($suggestions);
    }
}
