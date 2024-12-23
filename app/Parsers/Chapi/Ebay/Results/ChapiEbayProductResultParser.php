<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Results;

use App\Helpers\NotAllowed;
use App\Helpers\Price;
use App\Models\Search\ProductResult;
use Exception;
use DOMXPath;

final class ChapiEbayProductResultParser
{
    private static $paginationHandler;
    private static $vendor;
    private static $product;

    /**
     * @throws Exception
     */
    public static function parse($product, $vendor, $position): ProductResult
    {
        self::$product = $product;
        self::$vendor = $vendor;
        $productResult = new ProductResult();

        $productResult->setAttribute('position', $position);
        $productId = self::getProductId();
        if($productId == "123456"){
            return $productResult;
        }

        $title = self::getTitle();

        $notAllowed = new NotAllowed(self::$vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);
        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $productResult;
        }

        $productResult->setAttribute('product_id', $productId);
        $productResult->setAttribute('title', $title);
        $productResult->setAttribute('price', self::getPrice());

        $productResult->setAttribute('score', self::getScore());
        $productResult->setAttribute('rating', self::getRating());

        $image = self::getImage();

        $productResult->setAttribute('image', $image);
        $productResult->setAttribute('position', $position);

        return $productResult;
    }

    private static function getProductId(): string
    {
        $productId = null;

        $xpath = new DOMXPath(self::$product->ownerDocument);
        $element = $xpath->query('.//a', self::$product)->item(0);
        if ($element) {
            $href = $element->getAttribute('href');
            if ($href) {
                $path = parse_url($href, PHP_URL_PATH);
                if ($path) {
                    $itemId = substr($path, strpos($path, 'itm/') + 4);
                    if ($itemId) {
                        $productId = $itemId;
                    }
                }
            }
        }

        return (string) $productId;
    }

    private static function getTitle(): string
    {
        $title = '';

        $xpath = new DOMXPath(self::$product->ownerDocument);
        $element = $xpath->query('.//div[contains(@class, "s-item__title")]', self::$product)->item(0);
        if ($element) {
            $title = $element->textContent;
        }

        return (string) $title;
    }

    private static function getPrice(): float
    {
        $price = 0;

        $xpath = new DOMXPath(self::$product->ownerDocument);
        $priceElement = $xpath->query('.//span[contains(@class, "s-item__price")]', self::$product)->item(0);
        if ($priceElement) {
            $price = $priceElement->textContent;
            if (strpos($price, '$') === false && strpos($price, 'US') === false) {
                return (float) 0;
            }
            $price = (float) trim(str_replace(['$', 'US', 'MXN'], '', $price));
            $price = Price::pesosAdolar($price);

            return (float) $price;
        }

        return (float) $price;
    }

    private static function getScore(): int
    {
        $score = 0;

        $reviewCount = preg_replace('/[^0-9]/', '', self::getText(self::$product));

        if ($reviewCount) {
            if((int)$reviewCount > 10000){
                return 0;
            }
            return (int) $reviewCount;
        }

        return (int) $score;
    }

    private static function getRating(): float
    {
        $rating = 0;

        // Implementación para obtener la calificación si está disponible
        return (float) $rating;
    }

    private static function getImage(): string
    {
        $mainImage = '';

        $xpath = new DOMXPath(self::$product->ownerDocument);
        $imageElement = $xpath->query('.//img[contains(@class, "s-image")]', self::$product)->item(0);

        if(!$imageElement){
            $imageElement = $xpath->query('.//div[contains(@class, "s-item__image-wrapper")]//img', self::$product)->item(0);
        }

        if ($imageElement) {
            $mainImage = $imageElement->getAttribute('src');
        }

        return (string) $mainImage;
    }

    private static function getText($item): string
    {
        $text = '';

        $xpath = new DOMXPath($item->ownerDocument);
        $elements = $xpath->query('.//span[contains(@class, "s-item__reviews-count")]', $item);
        if ($elements->length > 0) {
            $text = $elements->item(0)->textContent;
        }

        return $text;
    }
}
