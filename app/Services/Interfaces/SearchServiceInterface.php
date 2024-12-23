<?php

declare(strict_types=1);

namespace App\Services\Interfaces;

use App\Models\Search\Result;
use Illuminate\Http\Request;

interface SearchServiceInterface
{
	/**
	 * Realiza una búsqueda de productos.
	 *
	 * @param string $query La consulta de búsqueda.
	 * @param int $page Número de página para la paginación de resultados.
	 * @return array Resultados de la búsqueda.
	 */
	public function searchProducts(Request $request, $facets, string $query, int $page): Result|array;

	public function fetchSearchResults(Request $request, string $query, int $page, $filters = null): \DOMXPath|array;
}
