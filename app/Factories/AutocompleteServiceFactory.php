<?php

declare(strict_types=1);

namespace App\Factories;

use App\Services\Chapi\Amazon\ChapiAmazonAutocompleteService;
use App\Services\Chapi\Homedepot\ChapiHomedepotAutocompleteService;
use App\Services\Chapi\Walmart\ChapiWalmartAutocompleteService;
use App\Services\Interfaces\AutocompleteServiceInterface;
use App\Services\Tm\Amazon\TmAmazonAutocompleteService;
use App\Services\Tm\Ebay\TmEbayAutocompleteService;
use InvalidArgumentException;
use App\Services\Chapi\Ebay\ChapiEbayAutocompleteService;

class AutocompleteServiceFactory
{
	public static function make(string $vendor, string $engineId): AutocompleteServiceInterface
	{
		if ($engineId === 'tm') {
			switch ($vendor) {
				case 'amazon':
					return new TmAmazonAutocompleteService();
				case 'ebay':
					return new TmEbayAutocompleteService();
				default:
					throw new InvalidArgumentException("Proveedor no soportado: {$vendor}");
			}
		} elseif ($engineId === 'direct' || $engineId === 'rainforest') {
            if ($vendor === 'amazon') {
                return new ChapiAmazonAutocompleteService();
            }elseif ($vendor === 'ebay') {
                return new ChapiEbayAutocompleteService();
            }elseif ($vendor === 'walmart') {
                return new ChapiWalmartAutocompleteService();
            }elseif ($vendor === 'homedepot') {
                return new ChapiHomedepotAutocompleteService();
            }
        }
	}
}
