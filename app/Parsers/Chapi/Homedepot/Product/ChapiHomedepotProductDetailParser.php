<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Homedepot\Product;

//use App\Models\Product\Feature;
use App\Helpers\NotAllowed;
use App\Models\Product\ProductDetails;
use App\Models\Product\Thumbnail;
use App\Parsers\Chapi\Homedepot\Product\ExtendedDetails\ChapiHomedepotExtendedDetailsParser;
use App\Services\CookieService;
use DOMXPath;

final class ChapiHomedepotProductDetailParser
{
    private $variantsParser;
    /**
     * @var DOMXPath
     */
    private $dom;

    /**
     * @var ProductDetails
     */
    private $product;

    /**
     * @const HOMEDEPOT_COOKIE
     */
    const HOMEDEPOT_COOKIE = "homedepot";
    private DOMXPath $xpath;

    public function __construct()
    {
        $this->cookieService = new CookieService(self::HOMEDEPOT_COOKIE);
        $this->chapiHomedepotExtendedDetailsParser = new ChapiHomedepotExtendedDetailsParser();
    }

    public function parse(
        $dom,
        $xpath,
        string $vendor,
        string $productId,
        string $cookie,
        $getRelatedProducts = false,
        $getHtml = false
    ): ProductDetails {
        $this->cookie = $cookie;
        $this->xpath = $xpath;
        $this->dom = $dom;
        $variants = [];
        $parentId = null;


        $scriptElement = $this->xpath->query('//script[@id="thd-helmet__script--productStructureData"]')->item(0);
        $jsonData = null;
        if ($scriptElement) {
            $jsonContent = $scriptElement->textContent;
            $jsonData = json_decode($jsonContent, true);
        }

        $this->product = new ProductDetails();

        if (!$jsonData) {
            return $this->product;
        }

        $productId = $jsonData['productID'];
        $title = $this->getTitle();

        $notAllowed = new NotAllowed($vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);
        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $this->product;
        }

        $images = $jsonData['image'];
        $this->product->setAttribute('product_id', $productId);
        $this->product->setAttribute('vendor', $vendor);
        $this->product->setAttribute('combination_separator', '');
        $this->product->setAttribute('image', $images[0]);
        $this->product->setAttribute('title', $title);

        $this->product->setAttribute('price', $this->getPrice($jsonData));
        $this->product->setAttribute('shipping_price', $this->getShippingPrice());
        $this->product->setAttribute('score', $this->getScore($jsonData));
        $this->product->setAttribute('rating', $this->getRating($jsonData));
        $this->product->setAttribute('parent_product_id', $parentId);
        $this->product->setAttribute('description', $this->getDescription($jsonData));

        if ($this->variantsParser) {
            if ($parentId === null) {
                $variants = $this->variantsParser->parse($this->product->getAttribute('product_id'));
            } else {
                $variants = $this->variantsParser->parse($this->product->getAttribute($parentId));
            }
        }

        $variantCombinations = [];

        $hasVariants = count($variants) > 0;

        $this->product->setAttribute('has_variants', $hasVariants);

        $features = $this->getFeatures();

        $this->product->setRelation('features', $features);
        $this->product->setRelation('variants', $variants);

        $this->product->setAttribute('has_combinations', $hasVariants ? true : false);
        $this->product->setRelation('combinations', $variantCombinations);

        $categories = $this->getCategories();

        $this->product->setRelation('relatedProducts', []);
        $this->product->setRelation('categories', $categories);
        $this->product->setRelation('videos', []);
        $this->product->setRelation('alsoBought', []);
        $this->product->setAttribute('breadcrumbs_flat', $this->getBreadcrumbsFlat($title, $categories));

        $this->product->setAttribute('engine', 'direct');
        $this->product->save();

        $this->product->setRelation('thumbnails', $this->getThumbnails($images));

        $extendedDetails = $this->chapiHomedepotExtendedDetailsParser->getData($this->dom, $this->xpath, $productId, $this->product->id);

        $isAllowedByBrand = $notAllowed->isAllowedByBrand($extendedDetails->brand);

        if(!$isAllowedByBrand){
            $this->product->setAttribute('price', 0);
            return $this->product;
        }

        $this->product->setRelation('extendedDetails', $extendedDetails);

        $this->product->setAttribute(
            'type',
            ($hasVariants) ? ProductDetails::TYPE_CONFIGURABLE : ProductDetails::TYPE_SIMPLE
        );

        return $this->product;
    }

    private function getThumbnails($thumbs): array
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

    private function getBreadcrumbsFlat($title, $categories): string
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

    private function getTitle(): string
    {
        $title = '';

        $element = $this->xpath->query('//span[@data-component="ProductDetailsTitle"]')->item(0);

        if(!$element){
            $element = $this->xpath->query('//div[contains(@class, "product-details__badge-title--wrapper")]//h1')->item(0);
        }

        if ($element) {
            $title = $element->textContent;
        }

        return $title;
    }

    private function getPrice($jsonData): float
    {
        $price = 0;
        if (isset($jsonData['offers']) && isset($jsonData['offers']['price'])) {
            $price = $jsonData['offers']['price'];
        }
        return (float) $price;
    }

    private function getScore($jsonData): int
    {
        $score = $jsonData['aggregateRating']['reviewCount'];
        return (int) $score;
    }

    private function getRating($jsonData): float
    {
        $rating = round($jsonData['aggregateRating']['ratingValue'] * 1, 1);
        return (float) $rating;
    }

    private function getDescription($jsonData): string
    {
        $description = '';

        if (isset($jsonData['description'])) {
            $description = $jsonData['description'];
        }

        return $description;
    }

    private function getFeatures(): array
    {
        $features = [];

        $featuresItems = $this->xpath->query('//div[contains(@class, "ux-layout-section-evo__item")]')->item(0);
        if (!$featuresItems) {
            return [];
        }
        $rows = $featuresItems->getElementsByTagName('div');

        foreach ($rows as $row) {
            $cols = $row->getElementsByTagName('div');
            foreach ($cols as $col) {
                $labelElement = $col->query('.//div[contains(@class, "ux-labels-values__labels")]')->item(0);
                $valueElement = $col->query('.//div[contains(@class, "ux-labels-values__values")]')->item(0);
                if ($labelElement && $valueElement) {
                    $label = strip_tags(trim($labelElement->textContent));
                    $value = strip_tags(trim($valueElement->textContent));
                    if (strtolower($label) === 'brand') {
                        $this->product->setAttribute('brand', $value);
                    }
                    if ($label && $value) {
                        $feature = new Feature();
                        if (strtolower(trim($label)) === 'condition') {
                            if (strpos(strtolower(trim($value)), 'new') !== false) {
                                $value = 'New';
                            }
                        }
                        $feature->setAttribute('title', trim($label));
                        $feature->setAttribute('feature', trim($value));
                        $features[] = $feature;
                    }
                }
            }
        }

        return $features;
    }

    private function getCategories(): array
    {
        $categories = [];

        return $categories;
    }

    private function getShippingPrice(): float
    {
        $shippingPrice = 0.0;
        return (float) $shippingPrice;
    }


}
