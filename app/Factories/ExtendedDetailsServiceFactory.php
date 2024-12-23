<?php

declare(strict_types=1);

namespace App\Factories;

use App\Services\Interfaces\ExtendedDetailsServiceInterface;
use InvalidArgumentException;
use App\Services\Chapi\Amazon\ExtendedDetailsAmazonService;
use App\Services\Chapi\Ebay\ExtendedDetailsEbayService;
use App\Services\Chapi\Homedepot\ExtendedDetailsHomedepotService;
use App\Services\Chapi\Walmart\ExtendedDetailsWalmartService;

class ExtendedDetailsServiceFactory
{
	public static function make(string $vendor): ExtendedDetailsServiceInterface|InvalidArgumentException
	{
        if ($vendor === 'amazon') {
            return new ExtendedDetailsAmazonService();
        } elseif ($vendor === 'ebay') {
            return new ExtendedDetailsEbayService();
        } elseif ($vendor === 'walmart') {
            return new ExtendedDetailsWalmartService();
        } elseif ($vendor === 'homedepot') {
            return new ExtendedDetailsHomedepotService();
        }

        return new InvalidArgumentException("Proveedor no soportado: {$vendor}");
	}
}
