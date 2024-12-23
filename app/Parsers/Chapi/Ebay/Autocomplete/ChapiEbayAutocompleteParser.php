<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Autocomplete;

final class ChapiEbayAutocompleteParser
{
	public static function getSugs($json)
	{
		$sugs = [];

		if (isset($json->res) && is_array($json->res->sug)) {
			foreach ($json->res->sug as $sug) {
				$sugs[] = $sug;
			}
		}

		return $sugs;
	}
}
