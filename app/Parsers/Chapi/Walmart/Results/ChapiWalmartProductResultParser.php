<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Results;

use App\Helpers\NotAllowed;
use App\Helpers\Price;
use App\Models\Search\ProductResult;
use Exception;

final class ChapiWalmartProductResultParser
{
	private static $paginationHandler;

	private static $vendor;

	private static $dom;
	/**
	 * @var mixed
	 */
	private static $product;

	/**
	 * @throws Exception
	 */
	public static function parse($product, $vendor, $position): ProductResult
	{
		self::$product = $product;
		self::$vendor = $vendor;
		$product = new ProductResult();

		$product->setAttribute('position', $position);

        $productId = self::getProductId();
        $title = self::getTitle();

        $notAllowed = new NotAllowed(self::$vendor);

        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);
        $isAllowedBySku = $notAllowed->isAllowedBySku($productId);

        if(!$isAllowedByKeyword || !$isAllowedBySku){
            return $product;
        }

		$product->setAttribute('product_id', $productId);
		$product->setAttribute('title', $title);
		$product->setAttribute('price', self::getPrice());

		$product->setAttribute('score', self::getScore());
		$product->setAttribute('rating', self::getRating());

		$image = self::getImage();

		$product->setAttribute('image', $image);
		$product->setAttribute('position', $position);

		return $product;
	}

	private static function getProductId(): string
	{
		if (isset(self::$product->usItemId) && self::$product->usItemId) {
			return (string) self::$product->usItemId;
		}

		return '';
	}

	private static function getTitle(): string
	{
		if (isset(self::$product->name) && self::$product->name) {
			return (string) self::$product->name;
		}

		return '';
	}
	private static function getPrice(): float
	{
        $price = 0.0;
		if (isset(self::$product->price) && self::$product->price) {
			$price =  (float) self::$product->price;
		}

        if($price){
            $price = Price::pesosAdolar($price);
        }

		return $price;
	}

	private static function getScore(): int
	{
        $score = 0;
		if (isset(self::$product->rating->numberOfReviews) && self::$product->rating->numberOfReviews) {
            $score = (int) self::$product->rating->numberOfReviews;
		}

        if($score > 10000){
            return 0;
        }

		return $score;
	}

	private static function getRating(): float
	{
		if (isset(self::$product->rating->averageRating) && self::$product->rating->averageRating) {
			return (float) self::$product->rating->averageRating;
		}

		return 0.0;
	}


	private static function getImage(): string
	{
		if (isset(self::$product->image) && self::$product->image) {
			return (string) self::$product->image;
		}
		return '';
	}

	private static function getText($item, $selector)
	{
		try {
			if (count($item->find($selector))) {
				return $item->find($selector, 0)->text();
			}
		} catch (Exception $e) {
			return '';
		}

		return '';
	}
}
