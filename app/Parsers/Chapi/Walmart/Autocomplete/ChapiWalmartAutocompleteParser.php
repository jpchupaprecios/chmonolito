<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Autocomplete;

final class ChapiWalmartAutocompleteParser
{
	public static function getSugs($json)
	{
		$sugs = [];

		if (isset($json->queries) && count($json->queries)) {
			foreach ($json->queries as $sug) {
				$sugs[] = $sug->displayName;
			}
		}

		return $sugs;
	}
}
