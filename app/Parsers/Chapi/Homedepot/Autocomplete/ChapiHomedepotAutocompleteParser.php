<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Homedepot\Autocomplete;

final class ChapiHomedepotAutocompleteParser
{
	public static function getSugs($json)
	{
		$sugs = [];

		if (isset($json->r) && count($json->r)) {
			foreach ($json->r as $sug) {
				$sugs[] = $sug->t;
			}
		}

		return $sugs;
	}
}
