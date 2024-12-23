<?php

declare(strict_types=1);

namespace App\Services\Chapi\Homedepot;

use App\Models\Product\ProductDetails;
use App\Parsers\Chapi\Homedepot\Product\ChapiHomedepotProductDetailParser;
use App\Services\CookieService;
use App\Services\Interfaces\ProductServiceInterface;
use Exception;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;
final class ChapiHomedepotProductService implements ProductServiceInterface
{
    private $productDetailParser;
    private string $cookie;
    /**
     * @var CookieService
     */
    private $cookieService;

    /**
     * @const HOMEDEPOT_COOKIE
     */
    const HOMEDEPOT_COOKIE = "homedepot";

    public function __construct(

    ) {
        $this->productDetailParser = new ChapiHomedepotProductDetailParser();
        $this->cookieService = new CookieService(self::HOMEDEPOT_COOKIE);
    }

    /**
     * @throws Exception
     */
    public function getProductDetails(
        Request $request,
        string $productId,
        string $vendor,
                $getRelatedProducts = false,
                $getHtml = false
    ): ProductDetails {
        $this->cookie = $this->cookieService->getCookie();//'session-id=140-4885816-6428851;session-id-time=2082787201l;ubid-main=130-9580061-8070705';
        $dom = $this->fetchProductDetails($request, $productId);
        $xpath = new DOMXPath($dom);
        return $this->productDetailParser->parse($dom, $xpath, $vendor, $productId, $this->cookie, $getRelatedProducts, $getHtml);
    }

    public function fetchProductDetails(Request $request, string $productId): \DOMDocument
    {
        $url = 'https://www.homedepot.com/p/Milwaukee-SHOCKWAVE-3-8-in-Drive-SAE-and-Metric-6-Point-Impact-Socket-Set-43-Piece-49-66-7009/' . $productId;
        //$this->cookie = "";
        $response = ChapiHomedepotWebContentService::scrape($url, $this->cookie, false);

        $dom = new DOMDocument();

        if(!$response){
            return new DOMXPath($dom);
        }

        $body = preg_replace('/\s\s+/', '', $response);
        $body = preg_replace('/\n/', '', $body);

        $start = strpos($body, "<div id='dp'");
        if ($start) {
            $bodyTmp = substr($body, $start);
            $end = strpos($bodyTmp, "<div id='be'");
            $html = substr($bodyTmp, 0, $end);
            $html = '<html><body>' . $html . '</html>';
        } else {
            $html = $body;
        }

        $dom = new DOMDocument();
        @$dom->loadHTML($response);
        return $dom;
    }
}
