<?php

declare(strict_types=1);

namespace App\Services\Chapi\Amazon;

use App\Models\Product\ProductDetails;
use App\Parsers\Chapi\Amazon\Product\ChapiAmazonProductDetailParser;
use App\Services\CookieService;
use App\Services\Interfaces\ProductServiceInterface;
use Exception;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
use App\Models\ScrapingSession;
final class ChapiAmazonProductService implements ProductServiceInterface
{
	private $productDetailParser;
    private string $cookie;
    private string $user_agent;

    /**
     * @var CookieService
     */
    private $cookieService;

    /**
     * @const AMAZON_COOKIE
     */
    const AMAZON_COOKIE = "amazon";

    public function __construct(

	) {
		$this->productDetailParser = new ChapiAmazonProductDetailParser();
        $this->cookieService = new CookieService(self::AMAZON_COOKIE);
	}

	/**
	 * @throws Exception
	 */
	public function getProductDetails(
		Request $request,
		string $productId,
		string $vendor,
		$getRelatedProducts = false,
		$getHtml = false,
        $ci = ""
	): ProductDetails {
        $this->cookie = "";//$this->cookieService->getCookie();//'session-id=140-4885816-6428851;session-id-time=2082787201l;ubid-main=130-9580061-8070705';
        $this->user_agent = "";
        if($ci){
            $scrapingSession = ScrapingSession::where('client_session_id', $ci)->first();
            if($scrapingSession){
                $this->cookie = $scrapingSession->amazon_cookie;
                $this->user_agent = $scrapingSession->user_agent;
            }
        }

		$dom = $this->fetchProductDetails($request, $productId);

		return $this->productDetailParser->parse($dom, $vendor, $productId, $this->cookie, $getRelatedProducts, $getHtml);
	}

	public function fetchProductDetails(Request $request, string $productId): \DOMXPath | array
	{
        $res = ChapiAmazonWebContentService::scrape('https://www.amazon.com/dp/' . $productId, $this->cookie, $this->user_agent, false);
        return ['result' => $res];
	}

}
