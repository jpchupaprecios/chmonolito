<?php

declare(strict_types=1);

namespace App\Services\Chapi\Ebay;

use App\Models\Search\Result;
use App\Parsers\Chapi\Ebay\Results\ChapiEbayResultParser;
use App\Services\CookieService;
use App\Services\Interfaces\SearchServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DOMDocument;
use DOMXPath;
final class ChapiEbaySearchService implements SearchServiceInterface
{
	private $resultParser;

	public static $vendor = 'ebay';

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

    /**
     * @const EBAY_COOKIE
     */
    const EBAY_COOKIE = "ebay";
    /**
     * @var CookieService
     */
    private $cookieService;

    public function __construct(

	) {
		$this->resultParser = new ChapiEbayResultParser();
        $this->cookieService = new CookieService(self::EBAY_COOKIE);
	}

	public function searchProducts(Request $request, $facets, string $query, int $page): Result
	{
		$result = $this->fetchSearchResults($request, $query, $page);

		return $this->resultParser->parse($result, self::$vendor, $query, $page);
	}

	public function fetchSearchResults(Request $request, string $query, int $page, $filters = null): \DOMXPath
	{
        $parsedQuery = urlencode($query);
        $country = 'us';
        $alreadySold = false;
        $condition = 'all';
        $type = 'all';

        $alreadySoldString = $alreadySold ? '&LH_Complete=1&LH_Sold=1' : '';
        $parsedQuery = urlencode($query);
        $url = 'https://www.ebay' . self::$countryDict[$country] . '/sch/i.html?_nkw=' . $parsedQuery .
            $alreadySoldString . self::$conditionDict[$condition] . self::$typeDict[$type] . '&_pgn=' . $page;

        $cookie = $this->cookieService->getCookie();

		$response = ChapiEbayWebContentService::scrape($url, $cookie);
		if ($response) {
			$response = self::getBodyContent($response);
		}

        $dom = new DOMDocument();
        @$dom->loadHTML($response);
        return new DOMXPath($dom);
	}

	private static function getBodyContent($html)
	{
		$pattern = '/<body\b[^>]*>(.*?)<\/body>/is';
		preg_match($pattern, $html, $matches);

		if (empty($matches)) {
			return $html;
		}

		return $matches[1] ?? '';
	}
}
