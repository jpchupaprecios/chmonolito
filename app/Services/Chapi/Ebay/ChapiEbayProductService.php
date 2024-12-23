<?php

declare(strict_types=1);

namespace App\Services\Chapi\Ebay;

use App\Models\Product\ProductDetails;
use App\Parsers\Chapi\Ebay\Product\ChapiEbayProductDetailParser;
use App\Services\CookieService;
use App\Services\Interfaces\ProductServiceInterface;
use Exception;
use Illuminate\Http\Request;
use DOMDocument;
use DOMXPath;

class ChapiEbayProductService implements ProductServiceInterface
{
    protected $productDetailParser;
    /**
     * @var CookieService
     */
    private $cookieService;

    /**
     * @const EBAY_COOKIE
     */
    const EBAY_COOKIE = "ebay";

    public function __construct(

    ) {
        $this->productDetailParser = new ChapiEbayProductDetailParser();
        $this->cookieService = new CookieService(self::EBAY_COOKIE);
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
        $result = $this->fetchProductDetails($request, $productId);

        $dom = $result["dom"];
        $status = $result["status"];

        $xpath = new DOMXPath($dom);
        return $this->productDetailParser->parse($dom, $xpath, $status, $vendor, $productId, "", $getRelatedProducts, $getHtml);
    }

    public function fetchProductDetails(Request $request, string $productId): DOMDocument | array
    {
        $variantId = $request->input("variantId", null);

        $cookie = $this->cookieService->getCookie();

        $ids = explode("|", $productId);

        if(isset($ids[1]) && $ids[1]){
            $variantId = $ids[1];
            $productId = $ids[0];
        }

        $url = "https://www.ebay.com/itm/{$productId}";
        if($variantId){
            $url .= "?var=".$variantId;
        }

        $response =  ChapiEbayWebContentService::scrape($url, $cookie);

        $dom = new DOMDocument();

        if(!$response){
            return [
                "status" => "error",
                "message" => "Ended",
                "dom" => $dom
            ];
        }

        @$dom->loadHTML($response);

        if(strpos($response, "This listing was ended") !== false || strpos($response, "Este anuncio se vendió")){
            return [
                "status" => "error",
                "message" => "Ended",
                "dom" => $dom
            ];
        }

        return [
            "status" => "ok",
            "message" => "",
            "dom" => $dom
        ];

	}

}
