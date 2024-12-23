<?php

declare(strict_types=1);

namespace App\Services\Chapi\Walmart;

use App\Parsers\Chapi\Walmart\Autocomplete\ChapiWalmartAutocompleteParser;
use App\Services\Interfaces\AutocompleteServiceInterface;

final class ChapiWalmartAutocompleteService implements AutocompleteServiceInterface
{
	/**
	 * Constructor de TmAmazonAutocompleteService.
	 *
	 * Aquí puedes inyectar dependencias si es necesario, como un cliente HTTP.
	 */
	public function __construct() {}

	/**
	 * Obtiene sugerencias de autocompletado de Amazon basadas en una consulta.
	 *
	 * @param string $query La consulta para autocompletar.
	 * @param string $vendor El proveedor, que en este caso siempre será 'amazon'.
	 * @return array Una lista de sugerencias.
	 */
	public function getSuggestions(string $query): array
	{
		$suggestions = $this->fetchSuggestions($query);

		return ChapiWalmartAutocompleteParser::getSugs($suggestions);
	}

	/**
	 *
	 * @param string $query
	 * @return array
	 */
	private function fetchSuggestions(string $query)
	{
		$query = urldecode($query);
		$url = 'https://www.walmart.com.mx/typeahead/v3/complete?term=' . $query . '&num=10';

		$url = str_replace(' ', '+', trim($url));

		return @json_decode(ChapiWalmartWebContentService::scrape($url));
	}
}
