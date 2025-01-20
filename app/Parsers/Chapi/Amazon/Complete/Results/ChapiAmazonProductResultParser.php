<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Results;

use App\Helpers\NotAllowed;
use App\Models\Search\ProductResult;
use DOMElement;
use DOMXPath;
use DOMDocument;
final class ChapiAmazonProductResultParser
{
    /**
     * @var DOMElement
     */
    private static DOMElement $product;
    protected static string $vendor;

    public static function parse($product, $vendor, $position): ProductResult
    {
        $hmtl = self::getElementHtml($product);
        self::$product = $product;
        self::$vendor = $vendor;
        $productResult = new ProductResult();

        $productResult->setAttribute('position', $position);

        $productId = self::getProductId();

        $productResult->setAttribute('product_id', $productId);
        $title = self::getTitle();

        $notAllowed = new NotAllowed(self::$vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);
        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $productResult;
        }

        $productResult->setAttribute('title', $title);

        $price = self::getPrice();
        $shippingPrice = self::getShippingPrice();
        if ($shippingPrice && $shippingPrice > 0) {
            $price = $price + $shippingPrice;
        }

        $productResult->setAttribute('price', $price);
        $productResult->setAttribute('score', self::getScore());
        $productResult->setAttribute('rating', self::getRating());
        $productResult->setAttribute('image', self::getImage());

        return $productResult;
    }

    protected static function getProductId(): string
    {
        $element = self::$product->getAttribute('data-asin');
        return $element ? (string) $element : '';
    }

    protected static function getTitle(): string
    {
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $titleElement = $xpath->query('.//h2[contains(@class, "a-text-normal")]', self::$product)->item(0);
        $titleElement = $titleElement ? trim(utf8_decode($titleElement->textContent)) : '';
        $titleElement = mb_convert_encoding($titleElement, 'UTF-8', 'ISO-8859-1');
        return $titleElement;
    }

    protected static function getPrice(): float
    {
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $priceElement = $xpath->query(
            './/span[contains(@class, "a-price")]/span[contains(@class, "a-offscreen")]',
            self::$product
        )->item(0);

        if ($priceElement) {
            return self::parsePrice($priceElement->textContent);
        }

        $priceElements = $xpath->query('.//span[contains(@class, "a-color-base")]', self::$product);
        foreach ($priceElements as $txt) {
            if (strpos($txt->textContent, '$') !== false) {
                return self::parsePrice($txt->textContent);
            }
        }

        return 0;
    }

    protected static function getShippingPrice(): float
    {
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $shippingElement = $xpath->query('.//div[@data-cy="delivery-recipe"]/div/span', self::$product)->item(0);

        $price = 0;
        if ($shippingElement) {
            $shippingText = $shippingElement->getAttribute('aria-label');
            if($shippingText){
                $tokens = explode(' ', $shippingText);
                foreach($tokens as $token){
                    if(strpos($token, '$') !== false){
                        $price = $token;
                        break;
                    }
                }

            }
            if($price){
                if (preg_match('/US\$([0-9,.]+)/', $price, $matches) && !self::containsFreeShipping($price)) {
                    return self::parsePrice($matches[1]);
                }elseif (preg_match('/\$([0-9,.]+)/', $price, $matches) && !self::containsFreeShipping($price)) {
                    return self::parsePrice($matches[1]);
                }
            }
        }

        return 0;
    }

    private static function containsFreeShipping(string $text): bool
    {
        $lowerText = strtolower($text);
        return strpos($lowerText, 'gratis') !== false || strpos($lowerText, 'free') !== false;
    }

    private static function parsePrice(string $price): float
    {
        $price = str_replace(['US$', '$', 'US', ','], '', $price);
        return (float) $price;
    }

    protected static function getScore(): int
    {
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $reviewsCountElement = $xpath->query('.//span[contains(@class, "a-size-base")]', self::$product)->item(0);

        if ($reviewsCountElement) {
            $reviewsCount = str_replace(',', '.', $reviewsCountElement->textContent);
            return is_numeric($reviewsCount) ? (int) $reviewsCount * 1000 : 0;
        }
        return 0;
    }

    protected static function getRating(): float
    {
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $reviewsRatingElement = $xpath->query('.//span[contains(@class, "a-icon-alt")]', self::$product)->item(1);

        if (!$reviewsRatingElement) {
            $ratingText = $xpath->query('.//span[contains(@class, "a-icon-alt")]', self::$product)->item(0);
            if ($ratingText) {
                $ar = explode(' ', $ratingText->textContent);
                foreach ($ar as $value) {
                    if ($value !== '5' && is_numeric($value)) {
                        return (float) $value;
                    }
                }
            }
        }
        return $reviewsRatingElement ? (float) $reviewsRatingElement->textContent : 0;
    }

    protected static function getImage(): string
    {
        $xpath = new DOMXPath(self::$product->ownerDocument);
        $mainImage = $xpath->query('.//img[contains(@class, "s-image")]', self::$product)->item(0);
        return $mainImage ? (string) $mainImage->getAttribute('src') : '';
    }

    private static function getElementHtml($element) {
        $dom = new DOMDocument();
        $dom->appendChild($dom->importNode($element, true));
        return $dom->saveHTML();
    }
}
