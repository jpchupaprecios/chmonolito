<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Product;

//use App\Models\Product\Feature;
use App\Helpers\NotAllowed;
use App\Models\Product\ProductDetails;
use App\Models\Product\Thumbnail;
use App\Parsers\Chapi\Walmart\Product\ExtendedDetails\ChapiWalmartExtendedDetailsParser;
use App\Parsers\Chapi\Walmart\Product\Variants\ChapiWalmartVariantsParser;
use App\Services\CookieService;
use DOMDocument;
use DOMXPath;

final class ChapiWalmartProductDetailParser
{
    private array $scriptData;
    private $cookie;
    private $product;
    /**
     * @var mixed|null
     */
    private $jsonData;

    /**
     * @const WALMART_COOKIE
     */
    const WALMART_COOKIE = "walmart";
    private DOMXPath $xpath;

    public function __construct()
    {
        $this->cookieService = new CookieService(self::WALMART_COOKIE);
        $this->chapiWalmartExtendedDetailsParser = new ChapiWalmartExtendedDetailsParser();
    }

    private function setJsonData(){
        $this->jsonData = null;

        $scripts = $this->xpath->document->getElementsByTagName('script');

        foreach ($scripts as $script) {
            if ($script->getAttribute('id') === '__NEXT_DATA__' && $script->getAttribute('type') === 'application/json') {
                $jsonContent = $script->nodeValue;

                $jsonData = json_decode($jsonContent, true);
                if(
                    isset($jsonData["props"]) &&
                    isset($jsonData["props"]["pageProps"]) &&
                    isset($jsonData["props"]["pageProps"]["initialData"]) &&
                    isset($jsonData["props"]["pageProps"]["initialData"]["data"]) &&
                    isset($jsonData["props"]["pageProps"]["initialData"]["data"]["product"])
                ){
                    $this->jsonData = $jsonData["props"]["pageProps"]["initialData"]["data"]["product"];
                }
                break;
            }
        }
    }
    public function parse($dom, $xpath, string $vendor, string $productId, array $cookie): ProductDetails
	{
        $this->xpath = $xpath;
        $this->dom = $dom;

        $this->setJsonData();

        $this->product = new ProductDetails();

        $this->cookie = $cookie;
        $variantsParser = new ChapiWalmartVariantsParser($xpath, $this->jsonData);

        $title = $this->getTitle($xpath);

        $notAllowed = new NotAllowed($vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);
        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $this->product;
        }

        $images = $this->getImages($xpath);
        $image = $images['main'] ?? '';

        $this->product->setAttribute('product_id', $productId);
        $this->product->setAttribute('vendor', $vendor);
        $this->product->setAttribute('image', $image);
        $this->product->setAttribute('title', $title);
        $this->product->setAttribute('brand', $this->getBrand($xpath));

        $price = $this->getPrice($xpath);

        $this->product->setAttribute('price', $price);
        $this->product->setAttribute('shipping_price', 0.0);
        $this->product->setAttribute('score', $this->getScore($xpath));
        $this->product->setAttribute('rating', $this->getRating($xpath));
        $this->product->setAttribute('description', $this->getDescription($xpath));



        $parentId = $this->getParentAsin();
        $variantCombinations = [];

        $variants = $variantsParser->parse($this->product->getAttribute('product_id'));

        if($variants){
            $variantCombinations = $variants["variantCombinations"];
            $variants = $variants["variantsGroup"];
        }

        $hasVariants = count($variants) > 0;
        $this->product->setAttribute('has_variants', $hasVariants);

        $thumbnails = $images['thumbnails'] ?? [];
        $features = $this->getFeatures($xpath);

        $this->product->setRelation('features', $features);
        $this->product->setRelation('variants', $variants);
        $this->product->setAttribute('combination_separator', '-');

        $this->product->setAttribute('has_combinations', count($variantCombinations) > 0);
        $this->product->setRelation('combinations', $variantCombinations);

        $categories = $this->getCategories($vendor);
        $this->product->setRelation('relatedProducts', []);
        $this->product->setRelation('categories', $categories);
        $this->product->setRelation('videos', []);
        $this->product->setRelation('alsoBought', []);
        $this->product->setAttribute('breadcrumbs_flat', $this->getBreadcrumbsFlat($title, $categories));
        $this->product->setAttribute('parent_product_id', ($hasVariants) ? '' : $parentId);
        $this->product->setAttribute('type', ($hasVariants) ? ProductDetails::TYPE_CONFIGURABLE : ProductDetails::TYPE_SIMPLE);

        $this->product->setAttribute('engine', 'direct');
        $this->product->save();

        $this->product->setRelation('thumbnails', $this->getThumbnails($thumbnails));
        $extendedDetails = $this->chapiWalmartExtendedDetailsParser->getData($this->dom, $this->xpath, $productId, $this->product->id);

        $isAllowedByBrand = $notAllowed->isAllowedByBrand($extendedDetails->brand);

        if(!$isAllowedByBrand){
            $this->product->setAttribute('price', 0);
            return $this->product;
        }

        $this->product->setRelation('extendedDetails', $extendedDetails);

        return $this->product;
    }

    private function getTitle(DOMXPath $xpath): string
    {
        $titleElement = $this->xpath->query('//h1[@id="main-title"]')->item(0);
        $titleElement = $titleElement ? (trim($titleElement->textContent)) : '';
        return str_replace('%', ' Porciento ', $titleElement);
    }

    private function getThumbnails(array $thumbs): array
    {
        $thumbnails = [];
        foreach ($thumbs as $image) {
            $thumbnail = new Thumbnail();

            $thumbnail->setAttribute('link', $image);
            $thumbnail->setAttribute('product_details_id', $this->product->id);
            $thumbnail->save();
            $thumbnails[] = $thumbnail;
        }
        return $thumbnails;
    }

    private function getBrand(DOMXPath $xpath): string
    {
        $brandElement = $this->xpath->query('//a[contains(@class, "inline-button") and contains(@href, "facet=brand")]')->item(0);
        if ($brandElement) {
            return ucfirst(trim($brandElement->textContent));
        }
        return '';
    }

    private function getPrice(DOMXPath $xpath): float
    {
        $dolar = env('DOLAR_PRICE', 0);
        $currentPrice = 0.0;
        $priceSelectors = [
            '//span[@itemprop="price"]',
        ];

        foreach ($priceSelectors as $selector) {
            $priceElement = $this->xpath->query($selector)->item(0);
            if ($priceElement) {
                $tmpPrice = $this->price_format($priceElement->textContent);
                if ($tmpPrice > $currentPrice) {
                    $currentPrice = $tmpPrice;
                }
            }
        }

        if (!$currentPrice) {
            $buttonElement = $this->xpath->query('//button[contains(@class, "w_J0bV")]')->item(0);
            $spanElements = $this->xpath->query('.//span', $buttonElement);
            $p = false;
            foreach ($spanElements as $span) {

                if($span){
                    $p =  $this->price_format(trim($span->nodeValue));
                    if($p){
                        break;
                    }
                }
            }
            if($p){
                $currentPrice = $p;
            }
        }

        if (!$currentPrice) {

            $priceRange = $this->xpath->query('//div[contains(@class, "a-price-range")]//*[@class="a-price"]//*[@class="a-offscreen"]');
            foreach ($priceRange as $priceElement) {
                $tmpPrice = $this->price_format($priceElement->textContent);
                if ($tmpPrice > $currentPrice) {
                    $currentPrice = $tmpPrice;
                }
            }

        }

        if (!$currentPrice) {
            if(isset($this->jsonData["priceInfo"]) && isset($this->jsonData["priceInfo"]["currentPrice"]) && isset($this->jsonData["priceInfo"]["currentPrice"]["price"]) && $this->jsonData["priceInfo"]["currentPrice"]["price"]){
                $currentPrice = $this->jsonData["priceInfo"]["currentPrice"]["price"];
            }
        }

        if($currentPrice){
            $currentPrice = $currentPrice / $dolar;
            if($currentPrice){
                $currentPrice = (float) number_format($currentPrice, 2, '.', '');
            }
        }

        return $currentPrice;
    }

    private function getParentAsin(): string
    {
        return $this->scriptData['parentAsin'] ?? '';
    }

    private function getScore(DOMXPath $xpath): int
    {
        $scoreElement = $this->xpath->query('//span[@class="rating-number"]')->item(0);
        return $scoreElement ? intval($scoreElement->textContent) : 0;
    }

    private function getRating(DOMXPath $xpath): float
    {
        $ratingElement = $this->xpath->query('//span[@itemprop="ratingValue"]')->item(0);
        return $ratingElement ? floatval(trim($ratingElement->textContent)) : 0;
    }

    private function getImages(DOMXPath $xpath): array
    {
        $images = [];
        $imageElements = $this->xpath->query('//div[@data-testid="vertical-carousel-container"]//img');

        foreach ($imageElements as $imageElement) {
            $imageUrl = $imageElement->getAttribute('src');
            $images[] = $imageUrl;
        }

        $mainImageElement = $this->xpath->query('//div[@data-testid="hero-image-container"]//img')->item(0);
        $mainImage = $mainImageElement ? $mainImageElement->getAttribute('src') : '';

        return [
            'thumbnails' => $images,
            'main' => $mainImage,
        ];
    }

    private function getDescription(DOMXPath $xpath): string
    {
        $descriptionElement = $this->xpath->query('//div[@class="product-large-description"]')->item(0);
        return $descriptionElement ? trim($descriptionElement->textContent) : '';
    }

    private function getFeatures(DOMXPath $xpath): array
    {
        $features = [];
        $featureElements = $this->xpath->query('//div[@id="specifications"]//li');

        foreach ($featureElements as $featureElement) {
            $text = strip_tags(trim($featureElement->textContent));
            if (strpos($text, ' Dimensiones') !== false) {
                preg_match('/(\d+\.?\d*) x (\d+\.?\d*) x (\d+\.?\d*) cm/', $text, $matches);
                if (isset($matches[1], $matches[2], $matches[3])) {
                    $widthCm = $matches[1];
                    $heightCm = $matches[2];
                    $depthCm = $matches[3];
                    $features['Dimensiones'] = "{$widthCm} x {$heightCm} x {$depthCm} cm";
                }

                preg_match('/(\d+\.?\d*) kg/', $text, $weightMatches);
                if (isset($weightMatches[1])) {
                    $weightKg = $weightMatches[1];
                    $features['Peso'] = "{$weightKg} kg";
                }
            }
        }

        $result = [];
        foreach ($features as $key => $value) {
            $feature = new Feature();
            $feature->setAttribute('title', trim($key));
            $feature->setAttribute('feature', trim($value));
            $result[] = $feature;
        }

        return $result;
    }

    private function price_format(string $price): float
    {
        $price = str_replace(['US$', '$', 'US', ','], '', $price);
        return is_numeric($price) ? (float) $price : 0.0;
    }

    private function getCategories(string $vendor): array
    {
        return [];
    }

    private function getBreadcrumbsFlat(string $title, array $categories): string
    {
        $breadcrumbsFlat = array_map(fn($category) => $category->name, $categories);
        return empty($breadcrumbsFlat) ? (strlen($title) > 50 ? mb_substr($title, 0, 50, 'UTF-8') . '...' : $title) : implode(' > ', $breadcrumbsFlat);
    }

}
