<?php

declare(strict_types=1);

namespace App\Services\Chapi\Walmart;
ini_set('memory_limit', '-1');
use App\Models\Search\Result;
use App\Parsers\Chapi\Walmart\Results\ChapiWalmartResultParser;
use App\Services\CookieService;
use App\Services\Interfaces\SearchServiceInterface;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
final class ChapiWalmartSearchService implements SearchServiceInterface
{
	private $resultParser;

	public static $vendor = 'walmart';

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
     * @var CookieService
     */
    private $cookieService;

    /**
     * @const WALMART_COOKIE
     */
    const WALMART_COOKIE = "walmart";

    public function __construct(

	) {
		$this->resultParser = new ChapiWalmartResultParser();
        $this->cookieService = new CookieService(self::WALMART_COOKIE);
	}

	public function searchProducts(Request $request, $facets, string $query, int $page): Result
	{
		$result = $this->fetchSearchResults($request, $query, $page);

		return $this->resultParser->parse($result, self::$vendor, $query, $page);
	}

	public function fetchSearchResults(Request $request, string $query, int $page, $filters = null): \DOMXPath | array
	{
		$parsedQuery = urlencode($query);
		$url = 'https://www.walmart.com.mx/search?q=' . $parsedQuery;

        $cookie = $this->cookieService->getCookie();//'4070d04e13fc131ac46e47278b8ab572f3e6b49b5483a04ea7c139b97a3cd08c:0G25S1j+rEN31/Cvu+TwQ+RU1WGvkyQK8euG9L/Ii8pKmN9a7oVVbDhuctor53b1BXYUoBupc3UUYOj40t380Q==:1000:VpuWzlHWBe03MHG7iCoRMjzbJaRVVmtz8kL4D9nP2U0azsbTsT2ilGY0InjKZIpwmBZnYDVna/cR1lJU24Mz2kU9R5lXNFc3bnns8c0AzReF3iFG0fDhIqkrNaQjwQrFwMj2VG8a0T2crAv2mDwYFBtRa7v3aSvIzasNzvWL9Rtyabz6RTt296cAB8N5pJnmOzAib7RttCikCJldqY4KK5q5ii2M2VAqBbXEhxewUPE=';

		$response = ChapiWalmartWebContentService::scrape($url, $cookie);
        $response = self::removeStylesAndScripts($response);

        return [
			'dom' => $response['html'],
			'nextDataScript' => $response['nextDataScript'],
		];
	}


	private static function removeStylesAndScripts($html)
	{
        $nextDataScript = "";
        $start = strpos($html, '<script id="__NEXT_DATA__"');
        if($start){
            $end = strpos($html, '</script>', $start) + strlen('</script>');
            $scriptContent = substr($html, $start, $end - $start);

            $startPos = strpos($html, '<script id="__NEXT_DATA__"');
            if ($startPos !== false) {
                // Encuentra la posición del final de la etiqueta de apertura <script>
                $startPos = strpos($html, '>', $startPos) + 1;

                // Encuentra la posición de la etiqueta de cierre </script>
                $endPos = strpos($html, '</script>', $startPos);

                if ($endPos !== false) {
                    // Extrae el contenido entre las etiquetas <script> y </script>
                    $scriptContent = substr($html, $startPos, $endPos - $startPos);
                }
            }
            $nextDataScript = json_decode($scriptContent);



            //$html = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $html);
        }

        $html = preg_replace('/<style\b[^>]*>(.*?)<\/style>/is', '', $html);

		return [
			'html' => $html,
			'nextDataScript' => $nextDataScript,
		];
	}
}
