<?php

declare(strict_types=1);

namespace App\Factories;

use App\Services\Interfaces\VariantServiceInterface;
use App\Services\Tm\Amazon\TmAmazonVariantService;
use InvalidArgumentException;


class VariantServiceFactory
{
	public static function make(string $vendor, string $engineId): VariantServiceInterface
    {
		if ($engineId === 'tm') {
			switch ($vendor) {
				case 'amazon':
					return new TmAmazonVariantService();
				default:
					throw new InvalidArgumentException("Proveedor no soportado: {$vendor}");
			}
        }
	}
}
