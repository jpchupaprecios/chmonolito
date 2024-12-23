<?php

declare(strict_types=1);

namespace App\Factories;

use App\Services\Bigbox\Homedepot\BigboxHomedepotSearchService;
use App\Services\Bluecart\Walmart\BluecartWalmartSearchService;
use App\Services\Chapi\Amazon\ChapiAmazonSearchService;
use App\Services\Chapi\Homedepot\ChapiHomedepotSearchService;
use App\Services\Chapi\Walmart\ChapiWalmartSearchService;
use App\Services\Countdown\Ebay\CountdownEbaySearchService;
use App\Services\Interfaces\SearchServiceInterface;
use App\Services\Rainforest\Amazon\RainforestAmazonSearchService;
use App\Services\Tm\Amazon\TmAmazonSearchService;
use App\Services\Chapi\Ebay\ChapiEbaySearchService;
use App\Services\Tm\Ebay\TmEbaySearchService;
use InvalidArgumentException;

class SearchServiceFactory
{
	public static function make(string $vendor, string $engineId): SearchServiceInterface
	{
		if ($engineId === 'tm') {
			switch ($vendor) {
				case 'amazon':
					return new TmAmazonSearchService();
				case 'ebay':
					return new TmEbaySearchService();
				default:
					throw new InvalidArgumentException("Proveedor no soportado: {$vendor}");
			}
		} elseif ($engineId === 'direct') {
			if ($vendor === 'amazon') {
				return new ChapiAmazonSearchService();
			} elseif ($vendor === 'ebay') {
                return new ChapiEbaySearchService();
            } elseif ($vendor === 'walmart') {
                return new ChapiWalmartSearchService();
            } elseif ($vendor === 'homedepot') {
                return new ChapiHomedepotSearchService();
            }
		} elseif ($engineId === 'rainforest') {
            if ($vendor === 'amazon') {
                return new RainforestAmazonSearchService();
            }
        } elseif ($engineId === 'countdown') {
            if ($vendor === 'ebay') {
                return new CountdownEbaySearchService();
            }
        } elseif ($engineId === 'bluecart') {
            if ($vendor === 'walmart') {
                return new BluecartWalmartSearchService();
            }
        } elseif ($engineId === 'bigbox') {
            if ($vendor === 'homedepot') {
                return new BigboxHomedepotSearchService();
            }
        }
	}
}
