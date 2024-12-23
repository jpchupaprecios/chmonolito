<?php

declare(strict_types=1);

namespace App\Services\Chapi\Ebay;

use App\Parsers\Chapi\Ebay\Autocomplete\ChapiEbayAutocompleteParser;
use App\Services\CookieService;
use App\Services\Interfaces\AutocompleteServiceInterface;

final class ChapiEbayAutocompleteService implements AutocompleteServiceInterface
{
    /**
     * @const EBAY_COOKIE
     */
    const EBAY_COOKIE = "ebay";
    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct() {
        $this->cookieService = new CookieService(self::EBAY_COOKIE);
    }

	/**
	 * Obtiene sugerencias de autocompletado de Amazon basadas en una consulta.
	 *
	 * @param string $query La consulta para autocompletar.
	 * @param string $vendor El proveedor, que en este caso siempre será 'amazon'.
	 * @return array Una lista de sugerencias.
	 */
	public function getSuggestions(string $query): array
	{
		$query = urldecode($query);
		$query = str_replace(' ', '+', trim($query));
		$url = 'https://www.ebay.com/autosug?kwd=' . $query . '&sId=0&_ch=0&_rs=1&_ss=1&_sl=1&_richres=1&callback=0&_store=1&_help=1&_richsug=1&_eprogram=1&_td=1&_nearme=1';


        $cookie = $this->cookieService->getCookie();

		$response = ChapiEbayWebContentService::scrape($url, $cookie);

		if ($response) {
			$response = json_decode($response);
			return ChapiEbayAutocompleteParser::getSugs($response);
		}
		return [];
	}
}
