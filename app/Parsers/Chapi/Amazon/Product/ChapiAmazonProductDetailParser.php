<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Product;

use App\Helpers\FixHtml;
use App\Helpers\NotAllowed;
use App\Helpers\Price;
use App\Services\CookieService;
use App\Models\Product\ProductDetails;
use App\Models\Product\Thumbnail;
use App\Parsers\Chapi\Amazon\Product\Offers\ChapiAmazonOfferParser;
use App\Parsers\Chapi\Amazon\Product\Variants\ChapiAmazonVariantsParser;
use App\Services\Chapi\Amazon\ChapiAmazonWebContentService;
use App\Parsers\Chapi\Amazon\Product\ExtendedDetails\ChapiAmazonExtendedDetailsParser;
use App\Models\Product\Category;
use DOMDocument;
use DOMXPath;
use Illuminate\Support\Facades\Log;

/**
 *
 */
final class ChapiAmazonProductDetailParser
{
    /**
     * @var array
     */
    private array $scriptData;
    /**
     * @var
     */
    private $cookie;
    /**
     * @var
     */
    private $product;
    /**
     * @var ChapiAmazonOfferParser
     */
    private $offersParser;
    /**
     * @var CookieService
     */
    private $cookieService;
    /**
     * @var ChapiAmazonExtendedDetailsParser
     */
    private $chapiAmazonExtendedDetailsParser;

    /**
     *
     */
    const AMAZON_COOKIE = "amazon";
    /**
     * @var DOMDocument
     */
    private $dom;
    /**
     * @var DOMDocument
     */
    private $domOffersPrice;

    /**
     *
     */
    public function __construct()
    {
        $this->offersParser = new ChapiAmazonOfferParser();
        $this->cookieService = new CookieService(self::AMAZON_COOKIE);
        $this->chapiAmazonExtendedDetailsParser = new ChapiAmazonExtendedDetailsParser(self::AMAZON_COOKIE);
    }

    /**
     * @param $html
     * @param string $vendor
     * @param string $productId
     * @param string $cookie
     * @return ProductDetails
     */
    public function parse($html, string $vendor, string $productId, string $cookie): ProductDetails
    {
        $this->product = new ProductDetails();

        if (!isset($html['result']) || str_contains( $html['result'], 'unauthorized' ) ) {
            return $this->product;
        }

        $this->cookie = $cookie;
        $this->dom = new DOMDocument();

        $html = $html['result'];

        if(!$html){
            return $this->product;
        }

        @$this->dom->loadHTML((string) $html);

        if(strpos(strtolower(strtolower($html)), "vet approval required") !== false) {
            return $this->product;
        }

        $this->xpath = new DOMXPath($this->dom);

        $this->scriptData = $this->getScriptData((string) $html);

        $variantsParser = new ChapiAmazonVariantsParser($this->xpath);

        $images = $this->getImages();
        $image = $images['main'] ?? '';

        $this->product->setAttribute('product_id', $productId);
        $title = $this->getTitle();

        $notAllowed = new NotAllowed($vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);
        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $this->product;
        }


        $this->product->setAttribute('vendor', $vendor);
        $this->product->setAttribute('combination_separator', '');
        $this->product->setAttribute('image', $image);
        $this->product->setAttribute('title', $title);

        $price = $this->getPrice();
        $delivery = 0;
        if($price){
            $delivery = $this->getShippingPrice();
            if($delivery){
                $price = $price + $delivery;
            }
        }

        if (!$price) {
            Log::debug('NO PRICE');
            $xpathOffersPrice = $this->getDomOffers($productId);
            $price = $this->getOffersPrice($productId, $xpathOffersPrice);
        }

        if(!$price){
            Log::debug('NO PRICE2');
            $price = $this->getOffersPrice2($productId, $xpathOffersPrice);
        }

        if(!$price){
            Log::debug('NO PRICE3');
        }
        if($price){
            $price = Price::cotizarDolar($price);
        }
        $this->product->setAttribute('price', $price);


        $this->product->setAttribute('shipping_price', $delivery);
        $this->product->setAttribute('score', $this->getScore());
        $this->product->setAttribute('rating', $this->getRating());

        $parentId = $this->getParentAsin();

        $thumbnails = $images['thumbnails'] ?? [];

        $this->product->setRelation('combinations', []);
        $this->product->setRelation('relatedProducts', []);
        $this->product->setRelation('videos', []);
        $this->product->setRelation('alsoBought', []);

        $categories = $this->getCategories($vendor);
        $this->product->setAttribute('breadcrumbs_flat', $this->getBreadcrumbsFlat($title, $categories));

        $this->product->setAttribute('engine', 'direct');
        $this->product->setAttribute('type', ProductDetails::TYPE_SIMPLE);
        $this->product->setAttribute('has_variants', false);

        //$this->product->save();

        $variants = $variantsParser->parse($this->product->getAttribute('product_id'));

        $variantCombinations = [];

        $hasVariants = false;//count($variants) > 0;

        $this->product->setAttribute('variants', $variants);

        $this->product->setAttribute('has_combinations', $hasVariants);

        $this->product->setAttribute('parent_product_id', $hasVariants ? '' : $parentId);
        $this->product->setAttribute('type', $hasVariants ? ProductDetails::TYPE_CONFIGURABLE : ProductDetails::TYPE_SIMPLE);

        $this->product->setAttribute('has_variants', $hasVariants);
        $this->product->setAttribute('thumbnails', $this->getThumbnails($thumbnails));

        $this->product->setRelation('categories', $categories);
        //$this->product->categories()->saveMany($categories);

        $extendedDetails = $this->chapiAmazonExtendedDetailsParser->getData($this->dom, $this->xpath, $productId, $this->product->id);

        /*$isAllowedByBrand = $notAllowed->isAllowedByBrand($extendedDetails->brand);

        if(!$isAllowedByBrand){
            $this->product->setAttribute('price', 0);
            return $this->product;
        }

        */
        $this->product->setAttribute('extendedDetails', $extendedDetails);

        return $this->product;
    }

    /**
     * @return bool
     */
    private function getOffersShipping(): bool
    {
        $spans = $this->xpath->query('//span[@id="buybox-see-all-buying-choices"]//a');

        foreach ($spans as $span) {
            if (strpos(strtolower($span->textContent), "ver todas las opciones") !== false) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return string
     */
    private function getProductId(): string
    {
        return (string) $this->product->getAttribute('product_id');
    }

    /**
     * @return string
     */
    private function getTitle(): string
    {
        $titleElement = $this->xpath->query('//span[@id="productTitle"]')->item(0);
        $titleElement = $titleElement ? trim($titleElement->textContent) : '';
        return str_replace('%', ' Porciento ', $titleElement);
    }

    /**
     * @param array $thumbs
     * @return array
     */
    private function getThumbnails(array $thumbs): array
    {
        $thumbnails = [];
        foreach ($thumbs as $image) {
            $thumbnails[] = new Thumbnail([
                'link' => $image,
                'product_details_id' => $this->product->id,
            ]);
        }

        //$this->product->thumbnails()->saveMany($thumbnails);

        return $thumbnails;
    }

    /**
     * @return float
     */
    private function getPrice(): float
    {
        $currentPrice = 0.0;

        $priceUsed = $this->xpath->query( '//*[@id="usedBuySection"]' )->item(0);

        if( $priceUsed ){
            if( str_contains( strtolower($priceUsed->textContent), 'buy used:' ) || str_contains( strtolower($priceUsed->textContent), 'usado:' ) ){
                return 0.0;
            }
        }

        $priceSelectors = [
            '//*[@id="priceblock_ourprice"]',
            '//form//*[@class="a-button a-button-selected"]//*[@class="a-size-mini"]',
            '//*[@id="dp-container"]//*[@id="ppd"]//*[@class="a-price"]//*[@class="a-offscreen"]',
            '//*[@id="dp-container"]//*[@id="ppd"]//*[contains(@class, "a-price")]//*[@class="a-offscreen"]',
            '//*[@id="centerCol"]//*[contains(@class, "a-price-whole")]'
        ];

        foreach ($priceSelectors as $selector) {
            $priceElement = $this->xpath->query($selector)->item(0);
            if ($priceElement) {
                if( $priceElement->parentNode->parentNode->parentNode->tagName == 'tr' ) {
                    if( str_contains( strtolower( $priceElement->parentNode->parentNode->parentNode->textContent ), 'list price' ) ) {
                        continue;
                    }
                }
                $tmpPrice = $this->price_format($priceElement->textContent);
                if ($tmpPrice > $currentPrice) {
                    $currentPrice = $tmpPrice;
                }
            }
        }

        if ($currentPrice) {
            return $currentPrice;
        }

        $priceRange = $this->xpath->query('//div[contains(@class, "a-price-range")]//*[@class="a-price"]//*[@class="a-offscreen"]');
        foreach ($priceRange as $priceElement) {
            $tmpPrice = $this->price_format($priceElement->textContent);
            if ($tmpPrice > $currentPrice) {
                $currentPrice = $tmpPrice;
            }
        }

        if ($currentPrice < 1) {
            $priceRanges = $this->xpath->query('//*[@id="corePrice_desktop"][.//*[contains(@class, "a-price")] and .//*[contains(@class, "a-offscreen")]]//*[@class="a-offscreen"]');

            foreach($priceRanges as $priceRange){
                if($priceRange && $priceRange->textContent){
                    $currentPrice = $this->price_format($priceRange->textContent);
                    break;
                }
            }
        }

        return $currentPrice;
    }

    /**
     * @param $productId
     * @return DOMXPath|null
     */
    private function getDomOffers($productId)
    {
        $this->cookie = $this->cookieService->getCookie();

        $response = (new ChapiAmazonWebContentService)->scrape(
            'https://www.amazon.com/gp/product/ajax/ref=dp_aod_unknown_mbc?asin=' . $productId . '&m=&qid=&smid=&sourcecustomerorglistid=&sourcecustomerorglistitemid=&sr=&pc=dp&experienceId=aodAjaxMain',
            $this->cookie,
            false
        );

        $domOffersPrice = new DOMDocument();


        if(is_array($response)){
            if (!isset($response['result'])) {
                return null;
            }
            $htmlOffersPrice = $response['result'];
        }else{
            $htmlOffersPrice = $response;
        }

        $htmlOffersPrice = trim(str_replace("\n", '', $htmlOffersPrice));

        @$domOffersPrice->loadHTML((string) $htmlOffersPrice);

        return new DOMXPath($domOffersPrice);
    }

    /**
     * @param $productId
     * @return float
     */
    private function getOffersPrice($productId, $xpathOffersPrice)
    {

        if (!$xpathOffersPrice) {
            return 0.0;
        }

        $offersDiv = $xpathOffersPrice->query('//div[@id="aod-offer"]');

        $itera = 0;
        $price = 0;

        foreach ($offersDiv as $offerDiv) {
            $itera++;
            $priceElement = $xpathOffersPrice->query('//*[@class="a-offscreen"]', $offerDiv);
            $priceElement2 = $xpathOffersPrice->query('//*[@class="aok-offscreen"]', $offerDiv);

            if (!$priceElement) {
                continue;
            }

            if (strpos(strtolower($offerDiv->textContent), 'used') !== false) {
                continue;
            }

            $price = 0;
            if ($priceElement) {
                $itemPrice = $priceElement->item($itera -1);
                $itemPrice2 = $priceElement2->item($itera -1);

                    if ($itemPrice) {
                        $price = trim($itemPrice->textContent);

                        $shippingPrice = 0;
                        $pattern = '/\$(\d+\.\d{2})\s+delivery/';
                        if (preg_match($pattern, $offerDiv->textContent, $matches)) {
                            $shippingPrice = $matches[1];
                        }

                        if(!$shippingPrice){
                            $deliveryElement = $xpathOffersPrice->query('.//span[@data-csa-c-delivery-price]', $offerDiv);
                            if($deliveryElement){
                                $deliveryPrice = $deliveryElement->item(0)->getAttribute('data-csa-c-delivery-price');
                                if($deliveryPrice){
                                    $deliveryPrice = trim($deliveryPrice, "por");
                                    $deliveryPrice = trim($deliveryPrice);
                                }
                            }
                            if($deliveryPrice){
                                $shippingPrice = $deliveryPrice;
                            }
                        }


                        if (!$price) {
                            $price = trim($itemPrice2->textContent);
                        }

                        if ($price) {
                            $price = $this->price_format($price);

                            if($price && $shippingPrice){
                                $shippingPrice = $this->price_format($shippingPrice);
                                if($shippingPrice){
                                    $price += $shippingPrice;
                                }
                            }

                            break;
                        }
                    }

            }
        }

        return (float) $price;
    }

    /**
     * @param $productId
     * @return float
     */
    private function getOffersPrice2($productId, $xpathOffersPrice)
    {

        if (!$xpathOffersPrice) {
            return 0.0;
        }

        $offersDiv = $xpathOffersPrice->query('//div[@id="aod-offer"]');

        $itera = 0;
        $price = 0;

        foreach ($offersDiv as $offerDiv) {
            $itera++;
            $priceElement = $xpathOffersPrice->query('//*[@class="a-offscreen"]', $offerDiv);
            $priceElement2 = $xpathOffersPrice->query('//*[@class="aok-offscreen"]', $offerDiv);

            if (!$priceElement) {
                continue;
            }

            if (strpos(strtolower($offerDiv->textContent), 'used') !== false) {
                continue;
            }

            $price = 0;
            if ($priceElement) {
                $itemPrice = $priceElement->item($itera -1);
                $itemPrice2 = $priceElement2->item($itera -1);

                if ($itemPrice) {
                    $price = trim($itemPrice->textContent);

                    $shippingPrice = 0;
                    $pattern = '/\$(\d+\.\d{2})\s+delivery/';
                    if (preg_match($pattern, $offerDiv->textContent, $matches)) {
                        $shippingPrice = $matches[1];
                        if($shippingPrice == "GRATIS"){
                            $shippingPrice = 0;
                        }
                    }

                    if(!$shippingPrice){
                        $deliveryElement = $xpathOffersPrice->query('.//span[@data-csa-c-delivery-price]', $offerDiv);
                        if($deliveryElement){
                            $deliveryPrice = $deliveryElement->item(0)->getAttribute('data-csa-c-delivery-price');
                            if($deliveryPrice){
                                $deliveryPrice = trim($deliveryPrice, "por");
                                $deliveryPrice = trim($deliveryPrice);
                            }
                        }
                        if($deliveryPrice){
                            $shippingPrice = $deliveryPrice;
                            if($shippingPrice == "GRATIS"){
                                $shippingPrice = 0;
                            }
                        }
                    }


                    if (!$price) {
                        $price = trim($itemPrice2->textContent);
                    }

                    if ($price) {
                        $tmpPrice = $this->price_format($price);

                        if($tmpPrice){
                            $price = $tmpPrice;
                        }else{
                            $pricesWords = explode(' ', $price);

                            foreach($pricesWords as $pricesWord){
                                if(strpos($pricesWord, 'US$') !== false || strpos($pricesWord, 'US') !== false || strpos($pricesWord, '$') !== false){
                                    $tmpPrice = $this->price_format($pricesWord);
                                    if($tmpPrice){
                                        $price = $tmpPrice;
                                        breaK;
                                    }
                                }
                            }
                        }

                        if($price && $shippingPrice){
                            $shippingPrice = $this->price_format($shippingPrice);
                            if($shippingPrice){
                                $price += $shippingPrice;
                            }
                        }

                        break;
                    }
                }

            }
        }

        return (float) $price;
    }

    /**
     * @return string
     */
    private function getParentAsin(): string
    {
        return $this->scriptData['parentAsin'] ?? '';
    }

    /**
     * @return int
     */
    private function getScore(): int
    {
        $scoreElement = $this->xpath->query('//span[@id="acrCustomerReviewText"]')->item(0);
        return $scoreElement ? intval(explode(' ', $scoreElement->textContent)[0]) : 0;
    }

    /**
     * @return float
     */
    private function getRating(): float
    {
        $ratingElement = $this->xpath->query('//span[contains(@class, "reviewCountTextLinkedHistogram")]//span')->item(0);
        return $ratingElement ? floatval(trim($ratingElement->textContent)) : 0;
    }

    /**
     * @return array
     */
    private function getImages(): array
    {
        $images = [];
        $tes1 = false;

        $imageBlock = $this->xpath->document->textContent;
        if ($imageBlock) {
            $tes1 = trim($this->xpath->document->textContent);
        }

        if (!$tes1) {
            return $images;
        }

        $bb1 = null;
        $aa1 = substr($tes1, 0, strrpos($tes1, 'colorToAsin') - 4);
        $p = strpos($aa1, '[{"hiRes');
        if ($p) {
            $bb1 = substr($aa1, $p);
        }

        if ($bb1) {
            $bb1 = str_replace("\n", ' ', $bb1);
            $bb1 = substr($bb1, 0, strpos($bb1, '}]},'));
            $bb1 .= '}]';
            $s = trim($bb1);
            $images = json_decode($s, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                $s .= '}}]';
                $images = json_decode($s, true);
            }
        } else {
            $imgCanvas = $this->xpath->query('//*[@id="img-canvas"]')->item(0);
            if ($imgCanvas) {
                $dom = new DOMDocument();
                @$dom->loadHTML($imgCanvas->ownerDocument->saveHTML($imgCanvas));
                $imageElements = $dom->getElementsByTagName('img');
                $tmpImages = [];
                foreach ($imageElements as $image) {
                    if (strpos($image->getAttribute('src'), 'jpg') !== false) {
                        $tmpImages[] = $image->getAttribute('src');
                    }
                }

                $strimg = $tmpImages[0] ?? '';

                $images = [
                    [
                        'hiRes' => $strimg,
                        'thumb' => $strimg,
                        'large' => $strimg,
                        'main' => [
                            $strimg => [355, 355],
                            $strimg => [450, 450],
                            $strimg => [425, 425],
                            $strimg => [450, 450],
                            $strimg => [425, 425],
                            $strimg => [466, 466],
                            $strimg => [522, 522],
                            $strimg => [569, 569],
                            $strimg => [679, 679],
                        ],
                        'variant' => 'PT06',
                        'lowRes' => null,
                        'shoppableScene' => null,
                    ],
                ];
            }
        }

        $thumbnails = [];
        $main = '';
        if ($images) {
            foreach ($images as $image) {
                if (isset($image['variant']) && $image['variant'] === 'MAIN' && !$main) {
                    $main = $image['large'];
                }
                $thumbnails[] = $image['large'];
            }
        }

        return [
            'thumbnails' => $thumbnails,
            'main' => $main,
        ];
    }

    /**
     * @param string $price
     * @return float
     */
    private function price_format(string $price): float
    {
        $price = str_replace(['US$', '$', 'US', ','], '', $price);
        return is_numeric($price) ? (float) $price : 0.0;
    }

    /**
     * @param string $vendor
     * @return array
     */
    private function getCategories(string $vendor): array
    {
        $categoriesContainer = $this->xpath->query('//div[@id="wayfinding-breadcrumbs_container"]');
        $categoriesLi = [];
        if ($categoriesContainer) {
            $categoriesContainer = $categoriesContainer->item(0);
            $categoriesLi = $this->xpath->query('.//li//a', $categoriesContainer);
        }

        $categories = [];
        if ($categoriesLi) {
            foreach ($categoriesLi as $categorieLi) {
                $href = $categorieLi->getAttribute('href');
                $anchorText = $categorieLi->textContent;
                $title = "";
                if ($anchorText) {
                    $anchorText = FixHtml::formatHtml($anchorText);
                    $title = $anchorText;
                }
                $category = new Category();
                $category->setAttribute('link', $href);
                $category->setAttribute('name', $title);
                $category->setAttribute('product_details_id', $this->product->id);
                $categories[] = $category;
            }
        }

        return $categories;
    }

    /**
     * @param string $title
     * @param array $categories
     * @return string
     */
    private function getBreadcrumbsFlat(string $title, array $categories): string
    {
        $categorieFlat = "";
        foreach ($categories as $categorie) {
            $categorieFlat .= $categorie->name . " > ";
        }

        return trim($categorieFlat, " > ");
    }

    /**
     * @param $html
     * @return array
     */
    private function getScriptData($html): array
    {
        $scriptData = [];

        preg_match('/var obj = jQuery\.parseJSON\(\'(.*?)\'\);/', $html, $matches);
        if (isset($matches[1])) {
            $jsonStr = str_replace("\\'", "'", $matches[1]);
            $scriptData = json_decode($jsonStr, true);
        }

        return $scriptData;
    }

    /**
     * @return float
     */
    private function getShippingPrice(): float
    {
        $shippingElement = $this->xpath->query('//div[@id="deliveryBlockMessage"]//span[@data-csa-c-delivery-price]')->item(0);

        if ($shippingElement) {
            $shippingText = $shippingElement->getAttribute('data-csa-c-delivery-price');
            if (preg_match('/US\$([0-9,.]+)/', $shippingText, $matches) && !self::containsFreeShipping($shippingText)) {
                return self::parsePrice($matches[1]);
            }
            if (preg_match('/\$([0-9,.]+)/', $shippingText, $matches) && !self::containsFreeShipping($shippingText)) {
                return self::parsePrice($matches[1]);
            }
        }

        if ($this->getOffersShipping()) {
            return $this->getShippingOffer();
        }

        return 0;
    }

    /**
     * @return float
     */
    private function getShippingOffer(): float
    {
        $price = 0.0;
        $productPrice = $this->product->price;
        $productId = $this->product->product_id;
        $url = 'https://www.amazon.com/gp/product/ajax/ref=dp_aod_unknown_mbc?asin=' . $productId . '&m=&qid=&smid=&sourcecustomerorglistid=&sourcecustomerorglistitemid=&sr=&pc=dp&experienceId=aodAjaxMain';
        $this->cookie = $this->cookieService->getCookie();

        $contentxPath = ChapiAmazonWebContentService::scrape($url, $this->cookie, false);

        if (!$contentxPath || !isset($contentxPath["result"])) {
            return $price;
        }

        $dom = new DOMDocument();

        @$dom->loadHTML($contentxPath["result"]);

        $contentxPath = new DOMXPath($dom);

        if (!$contentxPath) {
            return $price;
        }

        $pricesList = $contentxPath->query('//div[contains(@class, "a-fixed-left-grid")]');

        foreach ($pricesList as $priceList) {
            $price = $contentxPath->query('.//span[contains(@class, "a-offscreen")]', $priceList)->item(0);
            if ($price) {
                $price = $price->textContent;
                $price = $this->price_format($price);
                if ($price == $productPrice) {
                    $shippingElement = $contentxPath->query("//span[@data-csa-c-delivery-price]", $priceList);
                    if ($shippingElement) {
                        $shippingElement = $shippingElement->item(0);
                        $shippingPrice = $shippingElement->textContent;
                        if ($shippingPrice) {
                            preg_match_all('/\$\d+/', $shippingPrice, $matches);
                            if (!empty($matches[0])) {
                                $shippingPrice = $matches[0][0];
                                $shippingPrice = $this->price_format($shippingPrice);
                                if ($shippingPrice) {
                                    return $shippingPrice;
                                }
                            }
                        }
                    }
                }
            }
        }

        return (float) ($price ?? 0.0);
    }

    /**
     * @param string $text
     * @return bool
     */
    private static function containsFreeShipping(string $text): bool
    {
        $lowerText = strtolower($text);
        return strpos($lowerText, 'gratis') !== false || strpos($lowerText, 'free') !== false;
    }

    /**
     * @param string $price
     * @return float
     */
    private static function parsePrice(string $price): float
    {
        $price = str_replace(['US$', '$', 'US', ','], '', $price);
        return (float) $price;
    }
}
