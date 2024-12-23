<?php

declare(strict_types=1);

namespace App\Services\Chapi\Homedepot;

use App\Parsers\Chapi\Homedepot\Autocomplete\ChapiHomedepotAutocompleteParser;
use App\Services\Interfaces\AutocompleteServiceInterface;

final class ChapiHomedepotAutocompleteService implements AutocompleteServiceInterface
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

		return ChapiHomedepotAutocompleteParser::getSugs($suggestions);
	}

	/**
	 *
	 * @param string $query
	 * @return array
	 */
	private function fetchSuggestions(string $query)
	{
		$query = urldecode($query);
		$url = 'https://www.homedepot.com/TA2/search?term=free' . $query . '&num=10';

		$url = str_replace(' ', '+', trim($url));

		return @json_decode(ChapiHomedepotWebContentService::scrape($url));
	}
}
