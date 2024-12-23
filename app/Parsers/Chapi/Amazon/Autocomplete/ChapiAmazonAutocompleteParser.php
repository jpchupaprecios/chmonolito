<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Autocomplete;

final class ChapiAmazonAutocompleteParser
{
	public static function getSugs($json)
	{
		$sugs = [];

		if (isset($json->suggestions) && count($json->suggestions)) {
			foreach ($json->suggestions as $sug) {
				$sugs[] = $sug->value;
			}
		}

		return $sugs;
	}
}
