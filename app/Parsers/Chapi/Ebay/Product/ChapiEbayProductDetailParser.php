<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Product;

use App\Helpers\FixHtml;
use App\Helpers\NotAllowed;
use App\Helpers\Price;
use App\Models\Product\Category;
use App\Models\Product\Feature;
use App\Models\Product\ProductDetails;
use App\Models\Product\Thumbnail;
use App\Parsers\Chapi\Ebay\Product\Variants\ChapiEbayVariantsParser;
use App\Services\Chapi\Ebay\ChapiEbayWebContentService;
use App\Parsers\Chapi\Ebay\Product\ExtendedDetails\ChapiEbayExtendedDetailsParser;
use App\Services\CookieService;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

final class ChapiEbayProductDetailParser
{
    /**
     * @var DOMDocument
     */
    private DOMDocument $dom;
    /**
     * @var ProductDetails
     */
    private ProductDetails $product;
    /**
     * @var string
     */
    private string $cookie;
    /**
     * @var ChapiEbayVariantsParser
     */
    private ChapiEbayVariantsParser $variantsParser;

    /**
     * @const EBAY_COOKIE
     */
    const EBAY_COOKIE = "ebay";
    /**
     * @var DOMXPath
     */
    private DOMXPath $xpath;

    /**
     *
     */
    public function __construct()
    {
        $this->cookieService = new CookieService(self::EBAY_COOKIE);
        $this->chapiEbayExtendedDetailsParser = new ChapiEbayExtendedDetailsParser();
    }

    /**
     * @param DOMDocument $dom
     * @param DOMXPath $xpath
     * @param string $status
     * @param string $vendor
     * @param string $productId
     * @param string $cookie
     * @return ProductDetails
     */
    public function parse(DOMDocument $dom, DOMXPath $xpath, string $status, string $vendor, string $productId, string $cookie): ProductDetails
    {
        $this->status = $status;

        $this->initializeParser($dom, $xpath, $cookie);
        $this->variantsParser = new ChapiEbayVariantsParser($this->xpath);

        $productId = $this->processProductId($productId);

        $this->product->setAttribute('product_id', $productId);
        $this->product->setAttribute('vendor', $vendor);
        $this->product->setAttribute('combination_separator', '_');

        $this->isEnded();

        if(!$this->setProductDetails()){
            return $this->product;
        }

        $notAllowed = new NotAllowed($vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($this->product->getAttribute('title'));

        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $this->product;
        }

        $variantCombinations = [];
        $variants = [];

        $hasVariants = false;
        $this->product->setAttribute('has_variants', $hasVariants);

        $this->product->setAttribute('has_variants', $hasVariants);
        $this->product->setAttribute('type', $hasVariants ? 'configurable' : 'simple');
        $this->product->setRelation('variants', $variants);

        $this->product->setAttribute('has_combinations', count($variantCombinations) > 0);
        $this->product->setRelation('combinations', $variantCombinations);

        $features = $this->getFeatures();
        $this->product->setRelation('features', $features);

        $this->product->setAttribute('engine', 'direct');
        $images = $this->getImages();
        $this->product->setAttribute('image', $images[0] ?? '');

        $this->product->save();

        $variants = $this->variantsParser->parse($this->product->getAttribute('product_id'), $this->product->id);

        $variantCombinations = [];
        if($variants && isset($variants["variantCombinations"]) && $variants["variantCombinations"]){
            foreach($variants["variantCombinations"] as $v){
                $variantCombinations[] = $v;
            }
        }

        if($variants){
            $variants = $variants["variantsGroup"];
        }

        $hasVariants = count($variants) > 0;

        $this->product->setRelation('combinations', $variantCombinations);
        $this->product->setRelation('variants', $variants);

        $this->product->setAttribute('has_combinations', $hasVariants ? true : false);

        $this->product->setAttribute('type', ($hasVariants) ? ProductDetails::TYPE_CONFIGURABLE : ProductDetails::TYPE_SIMPLE);
        $this->product->setAttribute('has_variants', $hasVariants);
        $this->product->setRelation('thumbnails', $this->getThumbnails($images));

        $categories = $this->getCategories();
        $this->product->setRelation('categories', $categories);
        $this->product->categories()->saveMany($categories);
        $this->product->combinations()->saveMany($variantCombinations);

        $extendedDetails = $this->chapiEbayExtendedDetailsParser->getData($this->dom, $this->xpath, $productId, $this->product->id);

        $isAllowedByBrand = $notAllowed->isAllowedByBrand($extendedDetails->brand);

        if(!$isAllowedByBrand){
            $this->product->setAttribute('price', 0);
            return $this->product;
        }

        $this->product->setRelation('extendedDetails', $extendedDetails);
        $this->product->categories()->saveMany($categories);

        return $this->product;
    }

    /**
     * @param DOMDocument $dom
     * @param DOMXPath $xpath
     * @param string $cookie
     * @return void
     */
    private function initializeParser(DOMDocument $dom, DOMXPath $xpath, string $cookie): void
    {
        $this->dom = $dom;
        $this->xpath = $xpath;
        $this->product = new ProductDetails();
        $this->cookie = $cookie;
    }

    /**
     * @param string $productId
     * @return string
     */
    private function processProductId(string $productId): string
    {
        $ids = explode('|', $productId);
        return implode('|', array_filter($ids));
    }

    /**
     * @return bool
     */
    private function setProductDetails(): bool
    {
        $price = $this->getPrice();

        if(!$price){
            return false;
        }

        $isShippingAvailable = $this->shippingAvailable();

        if(!$isShippingAvailable){
            return false;
        }

        $shippingPrice = $this->getShippingPrice();
        if($this->status == "error"){
            $price = 0;
            $shippingPrice = 0;
        }

        if ($shippingPrice && $shippingPrice > 0) {
            $price = $price + $shippingPrice;
        }

        $this->product->setAttribute('title', $this->getTitle());
        $this->product->setAttribute('price', $price);
        $this->product->setAttribute('shipping_price', $shippingPrice);
        $this->product->setAttribute('score', $this->getScore());
        $this->product->setAttribute('rating', $this->getRating());

        $categories = $this->getCategories();
        $this->product->setRelation('categories', $categories);
        $this->product->setAttribute('breadcrumbs_flat', $this->getBreadcrumbsFlat($this->product->getAttribute('title'), $categories));

        $this->product->setAttribute(
            'type',
            ($this->product->getAttribute('has_variants')) ? ProductDetails::TYPE_CONFIGURABLE : ProductDetails::TYPE_SIMPLE
        );

        return true;
    }

    /**
     * @return false
     */
    private function isEnded(){
        $spans = $this->xpath->query("//span[contains(@class, 'ux-textspans')]");
        foreach($spans as $span){
            Log::debug("span fin: " . $span->nodeValue);
            if(strpos($span->nodeValue, "Finalizado:") !== false || strpos($span->nodeValue, "se vendió")){
                $this->status = "error";
                break;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    private function getTitle(): string
    {
        $titleElement = $this->xpath->query("//h1[contains(@class, 'x-item-title__mainTitle')]//span");
        return $titleElement->length > 0 ? $titleElement->item(0)->nodeValue : '';
    }

    /**
     * @return float
     */
    private function getPrice(): float
    {
        $price = 0;

        $priceElement = $this->xpath->query("//span[contains(@class, 'x-price-primary')]//span");

        if (!$priceElement->length) {
            $priceElement = $this->xpath->query("//div[contains(@class, 'x-price-primary')]//span");
        }

        if ($priceElement->length > 0) {
            $price = $priceElement->item(0)->nodeValue;
        }

        if(strpos("".$price, "GBP") !== false){

            $priceElement = $this->xpath->query("//span[contains(@class, 'x-price-approx__price')]//span");
            if ($priceElement->length > 0) {
                $price = $priceElement->item(0)->nodeValue;
            }
        }

        $mexicanPesos = false;

        if ($price) {
            if(strpos("".$price, "MXN") !== false){
                $mexicanPesos = true;
            }
            $price = $this->formatPrice($price);
        }

        if (!$price) {
            $aproxPriceElement = $this->xpath->query("//span[contains(@class, 'x-price-primary')]");
            if ($aproxPriceElement->length > 0) {
                $aproxPrice = $this->xpath->query(".//span[contains(@class, 'ux-textspans')]", $aproxPriceElement->item(0));
                if ($aproxPrice->length > 0) {
                    if(strpos("".$price, "MXN") !== false){
                        $mexicanPesos = true;
                    }
                    $price = $this->formatPrice($aproxPrice->item(0)->nodeValue);
                }
            }
            if (!$price) {
                $aproxPriceElement = $this->xpath->query("//span[contains(@class, 'x-price-approx')]");
                if ($aproxPriceElement->length > 0) {
                    $aproxPrices = $this->xpath->query(".//span[contains(@class, 'ux-textspans')]", $aproxPriceElement->item(0));
                    foreach ($aproxPrices as $aproxPrice) {
                        if(strpos("".$price, "MXN") !== false){
                            $mexicanPesos = true;
                        }
                        $aproxPrice = $this->formatPrice($aproxPrice->nodeValue);
                        if ($aproxPrice) {
                            $price = $aproxPrice;
                            break;
                        }
                    }
                }
            }
        }

        if($mexicanPesos){
            $price = Price::pesosAdolar($price);
        }

        return (float)$price;
    }

    /**
     * @return float
     */
    private function getShippingPrice(): float
    {
        $shippingPrice = 0.0;

        $shippingLabel = $this->xpath->query("//div[contains(@class, 'ux-labels-values--shipping')]");
        if ($shippingLabel->length > 0) {
            $shippingValues = $this->xpath->query(".//div[contains(@class, 'ux-labels-values__values')]", $shippingLabel->item(0));
            if ($shippingValues->length > 0) {
                $textSpans = $this->xpath->query(".//span[contains(@class, 'ux-textspans')]", $shippingValues->item(0));
                if ($textSpans->length > 0) {
                    foreach ($textSpans as $textSpan) {
                        $shippingPrice = $this->formatPrice($textSpan->nodeValue);
                        if ($shippingPrice) {
                            return $shippingPrice;
                        }
                    }
                }
            }
        }

        return $shippingPrice;
    }

    /**
     * @return bool
     */
    private function shippingAvailable() : bool{
        $shippingLabel2 = $this->xpath->query("//div[contains(@class, 'ux-layout-section-module')]");

        if($shippingLabel2 && is_object($shippingLabel2) && count($shippingLabel2)){
            foreach($shippingLabel2 as $shippingLabel){
                if(strpos($shippingLabel->nodeValue, "No se hacen envíos a México") !== false){
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * @return int
     */
    private function getScore(): int
    {
        $scoreElement = $this->xpath->query("//span[@id='acrCustomerReviewText']");
        $score = $scoreElement->length > 0 ? intval(explode(' ', $scoreElement->item(0)->nodeValue)[0]) : 0;
        if($score > 10000){
            return 0;
        }

        return $score;
    }

    /**
     * @return float
     */
    private function getRating(): float
    {
        $ratingElement = $this->xpath->query("//span[contains(@class, 'reviewCountTextLinkedHistogram')]//span//a//span");
        return $ratingElement->length > 0 ? (float)trim($ratingElement->item(0)->nodeValue) : 0.0;
    }

    /**
     * @return array
     */
    private function getImages(): array
    {
        $imageElements = $this->xpath->query("//div[contains(@class, 'ux-image-carousel')]//img");
        $linklist = [];
        $imgSize = 's-l500';

        foreach ($imageElements as $image) {
            $url = $image->getAttribute('data-zoom-src');
            if ($url && is_string($url)) {
                $url = str_replace('w_', $imgSize, $url);
                if (!in_array($url, $linklist)) {
                    $linklist[] = $url;
                }
            }
        }

        if (empty($linklist)) {
            foreach ($imageElements as $image) {
                $url = $image->getAttribute('src');
                if (str_contains($url, 's-l64')) {
                    $url = str_replace('s-l64', $imgSize, $url);
                    if (!in_array($url, $linklist)) {
                        $linklist[] = $url;
                    }
                }else{
                    $linklist[] = $url;
                }
            }
        }

        return $linklist;
    }

    /**
     * @param array $thumbs
     * @return array
     */
    private function getThumbnails(array $thumbs): array
    {
        $thumbnails = [];

        foreach ($thumbs as $image) {
            if($image){
                $thumbnail = new Thumbnail();
                $thumbnail->setAttribute('link', $image);
                $thumbnail->setAttribute('product_details_id', $this->product->id);
                $thumbnail->save();
                $thumbnails[] = $thumbnail;
            }
        }

        return $thumbnails;
    }

    /**
     * @param string $title
     * @param array $categories
     * @return string
     */
    private function getBreadcrumbsFlat(string $title, array $categories): string
    {
        $breadcrumbsFlat = [];
        foreach ($categories as $category) {
            $breadcrumbsFlat[] = $category->name;
        }

        if (!$categories) {
            if (strlen($title) > 50) {
                return substr($title, 0, 50) . '...';
            }

            return $title;
        }

        return implode(' > ', $breadcrumbsFlat);
    }

    /**
     * @return array
     */
    private function getFeatures(): array
    {
        $features = [];

        $featuresItems = $this->xpath->query("//div[contains(@class, 'ux-layout-section-evo__item')]");
        if ($featuresItems->length == 0) {
            return [];
        }
        $rows = $this->xpath->query(".//div[contains(@class, 'ux-layout-section-evo__row')]", $featuresItems->item(0));
        if ($rows->length == 0) {
            return [];
        }

        foreach ($rows as $row) {
            $cols = $this->xpath->query(".//div[contains(@class, 'ux-layout-section-evo__col')]", $row);
            foreach ($cols as $col) {
                $label = $this->xpath->query(".//span[contains(@class, 'ux-labels-values__labels')]", $col);
                $value = $this->xpath->query(".//span[contains(@class, 'ux-labels-values__values')]", $col);
                if ($label->length > 0 && $value->length > 0) {
                    $labelText = strip_tags(trim($label->item(0)->nodeValue));
                    $valueText = strip_tags(trim($value->item(0)->nodeValue));
                    if (strtolower($labelText) === 'brand') {
                        $this->product->setAttribute('brand', $valueText);
                    }
                    if ($labelText && $valueText) {
                        $feature = new Feature();
                        if (strtolower(trim($labelText)) === 'condition') {
                            if (strpos(strtolower(trim($valueText)), 'new') !== false) {
                                $valueText = 'New';
                            }
                        }
                        $feature->setAttribute('title', substr(trim($labelText), 0, 255));
                        $feature->setAttribute('feature', substr(trim($valueText), 0, 255));
                        $features[] = $feature;
                    }
                }
            }
        }

        return $features;
    }

    /**
     * @return array
     */
    private function getCategories(): array
    {
        $categoriesContainer = $this->xpath->query("//nav[contains(@class, 'breadcrumbs')]");
        $categoriesLi = [];
        if($categoriesContainer){
            $categoriesContainer = $categoriesContainer->item(0);
            // ontengo los li de $categoriesContainer
            $categoriesLi = $this->xpath->query('.//li//a', $categoriesContainer);
        }

        $categories = [];
        if($categoriesLi){
            foreach($categoriesLi as $categorieLi){

                $href = $categorieLi->getAttribute('href');

                foreach( $categorieLi->childNodes as $child){

                    if(isset($child->tagName) && $child->tagName == "span"){
                        $anchorText = $categorieLi->childNodes[1]->textContent;
                        if($anchorText){
                            $anchorText = FixHtml::formatHtml($anchorText);
                            $title = $anchorText;
                        }
                        if(!$href || ! $title){
                            continue;
                        }
                        $category = new Category();
                        $category->setAttribute('link', $href);
                        $category->setAttribute('name', $title);
                        $category->setAttribute('product_details_id', $this->product->id);
                        $categories[] = $category;
                        break;
                    }
                }
            }
        }

        return $categories;
    }

    /**
     * @return void
     */
    public function setDescription(): void
    {
        $description = $this->getDescription();
        $this->product->description = $description;
    }

    /**
     * @return mixed|string
     */
    private function getDescription()
    {
        $description = '';

        $cookie =
            'totp=1705101834091.BiH9QF+nPimJmc5qOJCvzG22liOzkFRsMW4iOSXjh/QhanWKfImCGvwoNEJjYEUtD8rQYfkn233MkrYgQCrNCQ==.zAAPAOMZUdQAN4pj91hNsQZyx92PzdIzcreLW1wlU5o;' .
            'ebay=%5Ejs%3D1%5EsfLMD%3D0%5Esin%3Din%5Esbf%3D%23000004%5E;' .
            'dp1=bu1p/anVwYTEwNjIwMg**6963fe89^pbf/%23000e000e000000080800000006782cb09^u1f/Juan6963fe89^tzo/1a465a1a57e^expt/00017050888752316692312b^mpc/0%7C06782cb09^bl/USen-US6963fe89^;' .
            'ns1=BAQAAAYxL36hOAAaAAKUADWeCywkyNTk4OTc5NjQwLzA7ANgAU2eCywljNjl8NjAxXjE2OTkzOTI2MjM0NTReXjFeM3wyfDV8NHw3fDEwfDQyfDQzfDExXl5eNF4zXjEyXjEyXjJeMV4xXjBeMV4wXjFeNjQ0MjQ1OTA3NU3e3OfjjCpRkfWaCKYeWZGfN6wl;' .
            'shs=BAQAAAYzmXnBLAAaAAVUAD2eCyugyMTk1NjIyOTgxMDAyLDIOGywdB5e0hON280OUWCE+h9EUSQ**;' .
            '__uzmf=7f60001329ecba-d5c3-4e8f-bf93-5b2d3823d01f16993926211725696253846-a82ca8ba12824612361;' .
            '__uzmc=1264836124869;' .
            'forterToken=b1cefa7388d04e6cb2ae57f796c24a76_1705088870211__UDF43-m4_15ck;' .
            'cid=wtM7PKiwgzJaFf28%23367358784;' .
            '__uzme=9089;' .
            '__deba=xtL_Jb7qcxk29Np75p_cG98y_AoFPEA8TOjw1sN6EOU3JN9D2xjzHYkZyhXBaquv0Xk95H6XRNl9Qw_Zi9x1fEA8CtOD0o6o8VALg6bci4EX9XX_cMWAoo8ebJDMmym56KDgQ9MQ-RgyrA3Y6YTw4A==;' .
            '__gpi=UID=00000a42e803b4b1:T=1699392624:RT=1705088826:S=ALNI_MaFn4lyULqTsB4xWjJ2SEIEcDG_1g;' .
            'totp=1705102028091.bAFpw+Hxs4nVy+NROkBv/tGqOOn4ccisOJ75SWIH0R0bE+3jZrA+OnN/9hMlSCcSP4AtULYoMBrYm6GOEjGSEg==.zAAPAOMZUdQAN4pj91hNsQZyx92PzdIzcreLW1wlU5o;' .
            '__ssuzjsr2=a9be0cd8e;' .
            '__uzmcj2=8873073364303;' .
            '__gads=ID=6b49346ea61f5efa:T=1699392624:RT=1705088826:S=ALNI_MbQShFvHKVMA6Y06DTZPE6NBb8h0g;' .
            '__uzmaj2=729bbe46-8365-49f3-b4d3-d82920f859b4;' .
            '__uzmd=1705088875;' .
            '__ssds=2;' .
            'QuantumMetricUserID=d5005360b60cc666bc981896e75003e7;' .
            '__uzmbj2=1699392623;' .
            '__uzmdj2=1705088884;' .
            '__uzmb=1699392621;' .
            's=BAQAAAYxL36hOAAWAAAEACmWi6OhqdXBhMTA2MjAyAAMAAWWi6O4wAAwACmWi6O4yNTk4OTc5NjQwAD0ACmWi6O5qdXBhMTA2MjAyAKgAAWWi6OgxAPgAIGWi6O5hYmIxODU1MDE4YjBhNDlhMjEzNjY0NWJmZmZiN2YwOAFlAANloujuIzAxhrBMW3xUzbE+htd5d1PJt1acLzc*;' .
            '__gsas=ID=86476af63338156c:T=1699392624:RT=1699392624:S=ALNI_MbKXXCfXPWDDQhvTb_XVAPoZ_zVUQ;' .
            '__uzma=e29236e4-8c92-4de2-9fab-973b7f36f5eb;';

        $parentId = $this->product->getAttribute('parent_product_id');

        if ($parentId) {
            $url = 'https://vi.vipr.ebaydesc.com/ws/eBayISAPI.dll?ViewItemDescV4&item=' . $parentId . '&secureDesc=1&variationId=' . $this->product->getAttribute('product_id');
        } else {
            $url = 'https://vi.vipr.ebaydesc.com/ws/eBayISAPI.dll?ViewItemDescV4&item=' . $this->product->getAttribute('product_id') . '&secureDesc=1';
        }

        $response = ChapiEbayWebContentService::scrape($url, $cookie);
        if ($response) {
            $descDom = new DOMDocument();
            libxml_use_internal_errors(true);
            $descDom->loadHTML($response);
            libxml_clear_errors();
            $descXpath = new DOMXPath($descDom);
            $features = $descXpath->query("//div[@id='feature-bullets']//div[@class='textbox white']");
            if ($features->length > 0) {
                $liElements = $descXpath->query(".//li//font//span", $features->item(0));
                foreach ($liElements as $liElement) {
                    $description .= $liElement->nodeValue . "\n";
                }
            }
        }

        if (!$description) {
            $description = $this->product->getAttribute('title');
        }

        return $description;
    }

    /**
     * @param $price
     * @return float
     */
    private function formatPrice($price): float
    {
        $price = str_replace(['approx', '(', ')', '$', 'US', 'MXN', 'EUR'], '', $price);
        $price = str_replace(" ", "", $price);
        $price = str_replace(" ", "", $price);
        $price = trim($price);

        $price = str_replace(',', '', $price);

        return (float)$price;
    }
}
