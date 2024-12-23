<?php

declare(strict_types=1);

namespace App\Services\Chapi\Amazon;

use App\Parsers\Chapi\Amazon\Autocomplete\ChapiAmazonAutocompleteParser;
use App\Services\Interfaces\AutocompleteServiceInterface;

final class ChapiAmazonAutocompleteService implements AutocompleteServiceInterface
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
		$suggestions = $this->fetchSuggestionsFromAmazon($query);

		return ChapiAmazonAutocompleteParser::getSugs($suggestions);
	}

	/**
	 *
	 * @param string $query
	 * @return array
	 */
	private function fetchSuggestionsFromAmazon(string $query)
	{
		$query = urldecode($query);
		$url = 'https://completion.amazon.com/api/2017/suggestions?limit=11&prefix=' . $query . '&suggestion-type=WIDGET&suggestion-type=KEYWORD&page-type=Search&alias=aps&lop=en_US&plain-mid=1';

		$url = str_replace(' ', '+', trim($url));

		return @json_decode(ChapiAmazonWebContentService::scrape($url));
	}
}
