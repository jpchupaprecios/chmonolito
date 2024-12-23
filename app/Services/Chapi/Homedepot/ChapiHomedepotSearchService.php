<?php

declare(strict_types=1);

namespace App\Services\Chapi\Homedepot;

use App\Models\Search\Result;
use App\Parsers\Chapi\Homedepot\Results\ChapiHomedepotResultParser;
use App\Services\Interfaces\SearchServiceInterface;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
final class ChapiHomedepotSearchService implements SearchServiceInterface
{
	private $resultParser;

	public static $vendor = 'homedepot';

	private static $countryDict = [
		'au' => '.com.au',
		'at' => '.at',
		'be' => '.be',
		'ca' => '.ca',
		'ch' => '.ch',
		'de' => '.de',
		'es' => '.es',
		'fr' => '.fr',
		'hk' => '.com.hk',
		'ie' => '.ie',
		'it' => '.it',
		'my' => '.com.my',
		'nl' => '.nl',
		'ph' => '.ph',
		'pl' => '.pl',
		'sg' => '.com.sg',
		'uk' => '.co.uk',
		'us' => '.com',
	];

	private static $conditionDict = [
		'all' => '',
		'new' => '&LH_ItemCondition=1000',
		'opened' => '&LH_ItemCondition=1500',
		'refurbished' => '&LH_ItemCondition=2500',
		'used' => '&LH_ItemCondition=3000',
	];

	private static $typeDict = [
		'all' => '&LH_All=1',
		'auction' => '&LH_Auction=1',
		'bin' => '&LH_BIN=1',
		'offers' => '&LH_BO=1',
	];

	public function __construct(

	) {
		$this->resultParser = new ChapiHomedepotResultParser();
	}

	public function searchProducts(Request $request, $facets, string $query, int $page): Result
	{
		$result = $this->fetchSearchResults($request, $query, $page);

		$result = $this->resultParser->parse($result, self::$vendor, $query, $page);

		return $result;
	}

	public function fetchSearchResults(Request $request, string $query, int $page, $filters = null): \DOMXPath
	{
		$parsedQuery = urlencode($query);
		$url = 'https://www.homedepot.com/s/' . $parsedQuery . '?NCNI-5';

		$response = ChapiHomedepotWebContentService::scrape($url);

        $dom = new DOMDocument();
        @$dom->loadHTML($response);
        return new DOMXPath($dom);
	}
}
