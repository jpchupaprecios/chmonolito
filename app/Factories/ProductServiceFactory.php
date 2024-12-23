<?php

declare(strict_types=1);

namespace App\Factories;

use App\Services\Chapi\Amazon\ChapiAmazonProductService;
use App\Services\Chapi\Ebay\ChapiEbayProductService;
use App\Services\Chapi\Homedepot\ChapiHomedepotProductService;
use App\Services\Interfaces\ProductServiceInterface;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;
use App\Services\Chapi\Walmart\ChapiWalmartProductService;

final class ProductServiceFactory
{
    /**
     * @const string AMAZON_VENDOR
     */
    const AMAZON_VENDOR = 'amazon';

    /**
     * @const string EBAY_VENDOR
     */
    const EBAY_VENDOR = 'amazon';

    /**
     * @const string WALMART_VENDOR
     */
    const WALMART_VENDOR = 'amazon';

    /**
     * @const string HOMEDEPOT_VENDOR
     */
    const HOMEDEPOT_VENDOR = 'amazon';

    public static function make(string $vendor, string $engineId): ProductServiceInterface
    {
        Log::debug('vendor:' . $vendor);
        Log::debug('engineId:' . $engineId);
        if ($engineId === 'tm') {
            switch ($vendor) {
                case 'amazon':
                    return new TmAmazonProductService();
                case 'ebay':
                    return new TmEbayProductService();
                default:
                    throw new InvalidArgumentException("Proveedor no soportado: {$vendor}");
            }
        } elseif ($engineId === 'direct') {
            if ($vendor === 'amazon') {
                return new ChapiAmazonProductService();
            } elseif ($vendor === 'ebay') {
                return new ChapiEbayProductService();
            } elseif ($vendor === 'homedepot') {
                return new ChapiHomedepotProductService();
            } elseif ($vendor === 'walmart') {
                return new ChapiWalmartProductService();
            }
        } elseif ($engineId === 'rainforest') {
            if ($vendor === 'amazon') {
                //return new RainforestAmazonProductService();
            }
        } elseif ($engineId === 'axesso') {
            if ($vendor === 'amazon') {
                return new AxessoAmazonProductService();
            } elseif ($vendor === 'walmart') {
                return new AxessoWalmartProductService();
            }
        }

        return throwException("Engine no soportado: {$engineId}");
    }
}
