<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Homedepot\Results;

use App\Helpers\NotAllowed;
use App\Models\Search\ProductResult;
use Exception;
use DOMXPath;

final class ChapiHomedepotProductResultParser
{
    private static $paginationHandler;
    private static $vendor;
    private static $dom;
    /**
     * @var mixed
     */
    private static $product;
    /**
     * @var mixed
     */
    private static $jsonData;
    /**
     * @var int
     */
    private static $position;

    /**
     * @throws Exception
     */
    public static function parse($product, $vendor, $position, $jsonData = null): ProductResult
    {
        self::$product = $product;
        self::$vendor = $vendor;
        self::$jsonData = $jsonData;
        $productResult = new ProductResult();
        self::$position = $position - 1;
        $productResult->setAttribute('position', $position);

        $productId = self::getProductId();
        $title = self::getTitle();

        $notAllowed = new NotAllowed(self::$vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);
        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $productResult;
        }

        $productResult->setAttribute('product_id', $productId);
        $productResult->setAttribute('title', self::getTitle());
        $productResult->setAttribute('price', self::getPrice());
        $productResult->setAttribute('score', self::getScore());
        $productResult->setAttribute('rating', self::getRating());
        $productResult->setAttribute('image', self::getImage());
        $productResult->setAttribute('position', $position);

        return $productResult;
    }

    private static function getProductId(): string
    {
        $productId = null;
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $element = $xpath->query('.//*[@clickid]', self::$product)->item(0);
        if ($element) {
            $clickId = $element->getAttribute('clickid');
            if ($clickId) {
                $clickId = explode('_', $clickId);
            }
            if (is_array($clickId) && !empty($clickId)) {
                $productId = trim($clickId[count($clickId) - 1], '-');
            }
        }

        return (string) $productId;
    }

    private static function getTitle(): string
    {
        $title = '';
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $element = $xpath->query('.//h3/span', self::$product)->item(0);
        if ($element) {
            $title = $element->textContent;
        }

        if (!$title && isset(self::$jsonData[self::$position])) {
            if (isset(self::$jsonData[self::$position]->name)) {
                $title = self::$jsonData[self::$position]->name;
            }
        }

        return (string) $title;
    }

    private static function getPrice(): float
    {
        $price = 0;
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $priceElement = $xpath->query('.//*[contains(@class, "price-format__main-price")]', self::$product)->item(0);
        if ($priceElement) {
            $price = $priceElement->textContent;
            if (strpos($price, '$') === false && strpos($price, 'US') === false) {
                return (float) 0;
            }
            $price = str_replace(['$', 'US'], '', $price);
            return (float) $price;
        }

        if (!$price && isset(self::$jsonData[self::$position])) {
            if (isset(self::$jsonData[self::$position]->offers) && isset(self::$jsonData[self::$position]->offers->price)) {
                $price = self::$jsonData[self::$position]->offers->price;
            }
        }

        return (float) $price;
    }

    private static function getScore(): int
    {
        $score = 0;
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $element = $xpath->query('.//*[contains(@class, "ratings__count")]', self::$product)->item(0);
        if ($element) {
            $score = trim($element->textContent, '() ');
        }

        if (!$score && isset(self::$jsonData[self::$position])) {
            if (isset(self::$jsonData[self::$position]->aggregateRating) && isset(self::$jsonData[self::$position]->aggregateRating->reviewCount)) {
                $score = self::$jsonData[self::$position]->aggregateRating->reviewCount;
            }
        }

        if((int)$score > 10000){
            return 0;
        }

        return (int) $score;
    }

    private static function getRating(): float
    {
        $rating = 0;
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $element = $xpath->query('.//*[contains(@class, "stars")]', self::$product)->item(0);
        if ($element) {
            $style = $element->getAttribute('style');
            preg_match('/width:(\d+(\.\d+)?)%/', $style, $matches);
            $percentage = $matches[1] ?? 0;
            $fraction = ($percentage / 20);
            $rating = $fraction;
        }

        if (!$rating && isset(self::$jsonData[self::$position])) {
            if (isset(self::$jsonData[self::$position]->aggregateRating) && isset(self::$jsonData[self::$position]->aggregateRating->ratingValue)) {
                $rating = self::$jsonData[self::$position]->aggregateRating->ratingValue;
            }
        }

        return (float) $rating;
    }

    private static function getImage(): string
    {
        $mainImage = '';
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $image = $xpath->query('.//img', self::$product)->item(0);
        if ($image) {
            $src = $image->getAttribute('src');
            if ($src) {
                $mainImage = $src;
            }
        }

        if (!$mainImage && isset(self::$jsonData[self::$position])) {
            if (isset(self::$jsonData[self::$position]->image)) {
                $mainImage = self::$jsonData[self::$position]->image;
            }
        }

        return (string) $mainImage;
    }
}
