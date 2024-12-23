<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Factories\AutocompleteServiceFactory;
use App\Factories\ProductServiceFactory;
use App\Factories\SearchServiceFactory;
use App\Helpers\Price;
use App\Models\Product\ProductDetails;
use App\Models\Search\Result;
use App\Services\History\HistoryProductService;
use App\Services\Interfaces\AutocompleteServiceInterface;
use App\Services\Interfaces\ProductServiceInterface;
use App\Services\Interfaces\SearchServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Mockery\Exception;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use \App\Models\Exchange;

final class ApiController extends Controller
{
    protected AutocompleteServiceFactory $autocompleteFactory;
    protected SearchServiceFactory $searchServiceFactory;
    protected ProductServiceFactory $productFactory;
    protected AutocompleteServiceInterface $autocompleteService;
    protected SearchServiceInterface $searchService;
    protected ProductServiceInterface $productService;
    public const STATUS_OK = 'ok';
    public const STATUS_FAIL = 'fail';
    public const STATUS_NOT_FOUND = 'not_found';
    public const HTTP_UNPROCESSABLE_ENTITY = 'unprocessable_entity';

    public const HTTP_DISABLED = 'disabled';

    private mixed $cacheEnable;
    private mixed $timeLimitMinutesSearch;
    private mixed $timeLimitMinutesProduct;

    public function __construct(AutocompleteServiceFactory $autocompleteFactory, SearchServiceFactory $searchServiceFactory, ProductServiceFactory $productFactory, HistoryProductService $historyProductService)
    {
        $this->autocompleteFactory = $autocompleteFactory;
        $this->searchServiceFactory = $searchServiceFactory;
        $this->productFactory = $productFactory;
        $this->cacheEnable = env('CACHE_ENABLE');
        $this->timeLimitMinutesSearch = env('TIME_LIMIT_MINUTES_SEARCH', 60);
        $this->timeLimitMinutesProduct = env('TIME_LIMIT_MINUTES_PRODUCTS', 60);
        $this->historyProductService = $historyProductService;

    }

    public function autocomplete(Request $request, $query, $vendor, $engineId): JsonResponse
    {
        try {
            $this->autocompleteService = $this->autocompleteFactory->make($vendor, $engineId);
            $result = $this->autocompleteService->getSuggestions($query, $vendor);
            return response()->json(['status' => self::STATUS_OK, 'data' => $result])->setStatusCode(
                ResponseAlias::HTTP_OK,
                Response::$statusTexts[ResponseAlias::HTTP_OK]
            );
        } catch (Exception $e) {
            return response()->json(['status' => self::STATUS_FAIL, 'data' => [], 'error' => $e->getMessage()])->setStatusCode(
                ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                Response::$statusTexts[ResponseAlias::HTTP_INTERNAL_SERVER_ERROR]
            );
        }
    }

    public function search(Request $request, string $query, int $page, string $vendor, string $engineId, bool $debug = null): JsonResponse
    {

        try {
            $startSearch = microtime(true);
            $query = urldecode($query);
            $query = str_replace(' ', '+', $query);
            $searchResult = null;
            $engineIdAlias = $this->getEngineAlias($engineId);


            if($this->cacheEnable){
                $searchResult = Result
                    ::where("query", $query)
                    ->where("vendor", $vendor)
                    ->where("page", $page)
                    //->where("engine", $engineIdAlias)
                    ->with('products')
                    ->with('pagination')
                    ->with('refinements')
                    ->first();
            }

            if ($searchResult && $searchResult->created_at->lt(Carbon::now()->subMinutes($this->timeLimitMinutesSearch))) {
                Result::where('query', $query)
                    ->where('vendor', $vendor)
                    ->where('page', $page)
                    ->delete();
                $searchResult = false;
            }

            if($searchResult){
                return response()->json(['status' => self::STATUS_OK, 'data' => $searchResult->getData()])->setStatusCode(
                    ResponseAlias::HTTP_OK,
                    Response::$statusTexts[ResponseAlias::HTTP_OK]
                );
            }

            $this->searchService = $this->searchServiceFactory->make($vendor, $engineId);
            $facets = $request->input("facets", false);

            $debug = self::getDebug($debug);

            $result = $this->searchService->searchProducts($request, $facets, $query, $page, $debug);
            $resultDebbug = null;
            if(isset($result["debugging"])){
                $resultDebbug = $result["debugging"];
            }

            if(isset($result["result"])) {
                $result = $result["result"];
            }

            $data = $result->getData();

            if(!$data){
                $endtSearch = microtime(true);
                $totalSearch = $endtSearch - $startSearch;
                if($debug){
                    if(isset($result["debugging"])){
                        $result["debugging"]["methods"]["search"]["benckmark"] = $totalSearch;
                    }
                }
                return response()->json(['status' => self::STATUS_FAIL, 'data' => $result, 'error' => "sin resultados"])->setStatusCode(
                    ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                    Response::$statusTexts[ResponseAlias::HTTP_INTERNAL_SERVER_ERROR]
                );
            }

            $dolar = Price::getDolarPrice();

            $productsTmp = [];

            if($dolar){
                if(isset($data["products"]) && $data["products"]){
                    foreach($data["products"] as &$prod){
                        $price = Price::cotizarDolar($prod->price);
                        if($price){
                            $prod->price = $price;

                        }

                        $productsTmp[] = $prod;
                    }
                }
            }

            $data["products"] = $productsTmp;

            $result->page = $page;

            $result->engine = $engineIdAlias;

            if($resultDebbug){
                //$data = $results;
            }

            if($resultDebbug){
                $data["debug"] = $resultDebbug;
            }

            if(!empty($data["products"])){
                if(!$resultDebbug && $this->cacheEnable) {
                    $result->store();
                }
                $endtSearch = microtime(true);
                $totalSearch = $endtSearch - $startSearch;
                if($debug){
                    $data["debug"]["methods"]["search"]["benckmark"] = $totalSearch;
                }
                return response()->json(['status' => self::STATUS_OK, 'data' => $data])->setStatusCode(
                    ResponseAlias::HTTP_OK,
                    Response::$statusTexts[ResponseAlias::HTTP_OK]
                );
            }
        } catch (Exception $e) {
            return response()->json(['status' => self::STATUS_FAIL, 'data' => [], 'error' => $e->getMessage()])->setStatusCode(
                ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                Response::$statusTexts[ResponseAlias::HTTP_INTERNAL_SERVER_ERROR]
            );
        }

        return response()->json(['status' => self::STATUS_NOT_FOUND, 'data' => [], 'error' => 'Not Found'])->setStatusCode(
            ResponseAlias::HTTP_NOT_FOUND,
            Response::$statusTexts[ResponseAlias::HTTP_NOT_FOUND]
        );
    }


    public function product(Request $request, $productId, $vendor, $engineId): JsonResponse
    {
        try {
            $getRelatedProducts = $request->input('getRelatedProducts', false) === 'true';
            $getHtml = $request->input('getHtml', false) === 'true';

            $productResult = null;
            $engineIdAlias = $this->getEngineAlias($engineId);

            if($this->cacheEnable){
                $productResult = ProductDetails
                    ::where("product_id", $productId)
                    ->where("vendor", $vendor)
                    //->where("engine", $engineIdAlias)
                    ->with('variants')
                    ->with('categories')
                    //->with('videos')
                    ->with('combinations')
                    ->with('thumbnails')
                    //->with('relatedProducts')
                    ->with('extendedDetails')
                    //->with('alsoBought')
                    //->with('videos')
                    //->with('thumbnails')
                    //->with('categories')
                    //->with('features')
                    ->first();
            }

            if ($productResult && $productResult->created_at->lt(Carbon::now()->subMinutes($this->timeLimitMinutesProduct))) {
                ProductDetails
                    ::where("product_id", $productId)
                    ->where("vendor", $vendor)
                    ->delete();
                $productResult = false;
            }

            if($productResult){
                return response()->json(['status' => self::STATUS_OK, 'data' => $productResult->getData()])->setStatusCode(
                    ResponseAlias::HTTP_OK,
                    Response::$statusTexts[ResponseAlias::HTTP_OK]
                );
            }

            $this->productService = $this->productFactory->make($vendor, $engineId);
            $product = $this->productService->getProductDetails($request, $productId, $vendor, $getRelatedProducts, $getHtml);

            if ($product->getAttribute('product_id')) {
                if($product->getAttribute('title') && $product->getAttribute('image')){

                    $product->engine = $engineIdAlias;

                    //$product->price = 0;

                    if(!$product->price){
                        $product->delete();
                        return response()->json(['status' => self::STATUS_NOT_FOUND, 'data' => [], 'error' => 'Not Found'])->setStatusCode(
                            ResponseAlias::HTTP_NOT_FOUND,
                            Response::$statusTexts[ResponseAlias::HTTP_NOT_FOUND]
                        );
                    }

                    if($this->cacheEnable){
                        $product->save();
                    }

                    $data = $product->getData();


                    $dolar = Price::getDolarPrice();

                    if($this->cacheEnable) {
                        $this->historyProductService->setHistoryProduct($request, $data, $vendor);
                    }

                    $data["debugging"] = ["methods" => []];
                    $resultDebbug = $data["debugging"];


                    if($dolar){
                        $data["price"] = Price::cotizarDolar($data["price"]);
                    }

                    if(!$data["price"]){
                        return response()->json(['status' => self::HTTP_UNPROCESSABLE_ENTITY, 'data' => $data])->setStatusCode(
                            ResponseAlias::HTTP_OK,
                            Response::$statusTexts[ResponseAlias::HTTP_OK]
                        );
                    }

                    return response()->json(['status' => self::STATUS_OK, 'data' => $data])->setStatusCode(
                        ResponseAlias::HTTP_OK,
                        Response::$statusTexts[ResponseAlias::HTTP_OK]
                    );
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => self::STATUS_FAIL, 'data' => [], 'error' => $e->getMessage()])->setStatusCode(
                ResponseAlias::HTTP_INTERNAL_SERVER_ERROR,
                Response::$statusTexts[ResponseAlias::HTTP_INTERNAL_SERVER_ERROR]
            );
        }

        return response()->json(['status' => self::STATUS_NOT_FOUND, 'data' => [], 'error' => 'Not Found'])->setStatusCode(
            ResponseAlias::HTTP_NOT_FOUND,
            Response::$statusTexts[ResponseAlias::HTTP_NOT_FOUND]
        );

    }

    private function getEngineAlias($engineId)
    {
        if ($engineId === 'direct') {
            $engineId = 'ch';
        } elseif ($engineId === 'axesso') {
            $engineId = 'ax';
        } elseif ($engineId === 'rainforest') {
            $engineId = 'rf';
        } elseif ($engineId === 'countdown') {
            $engineId = 'cd';
        } elseif ($engineId === 'bluecart') {
            $engineId = 'bc';
        } elseif ($engineId === 'bigbox') {
            $engineId = 'bb';
        }

        return $engineId;
    }

    private static function getDebug($debug)
    {
        $debugScrape = env('DEBUG_SCRAPE', false);

        if ($debug) {
            return true;
        }

        return (bool) $debugScrape;
    }

}
