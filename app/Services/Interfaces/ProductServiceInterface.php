<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Product\ProductDetails;
use Illuminate\Http\Request;
use DOMXPath;

interface ProductServiceInterface
{
	/**
	 * Obtiene detalles de un producto específico.
	 *
	 * @param string $productId Identificador único del producto (ASIN para Amazon, por ejemplo).
	 * @param string $vendor El proveedor (Amazon, eBay, etc.).
	 * @return array Detalles del producto.
	 */
	public function getProductDetails(
		Request $request,
		string $productId,
		string $vendor,
		$getRelatedProducts = false,
		$getHtml = false
	): ProductDetails;

	public function fetchProductDetails(Request $request, string $productId): \DOMXPath | array | \DOMDocument;
}
