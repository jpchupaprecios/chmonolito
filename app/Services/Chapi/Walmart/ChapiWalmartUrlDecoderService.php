<?php

declare(strict_types=1);

namespace App\Services\Chapi\Walmart;

abstract class ChapiWalmartUrlDecoderService extends UrlDecoderService
{
	private static $seed;

	public static function decodeLink($linkUrl): string
	{
		$params = [];
		$res = '';
		$dataLink = self::decodificar($linkUrl);
		if ($dataLink) {
			$dataLink = explode('&', $dataLink);
			foreach ($dataLink as $dl) {
				if (!$dl) {
					continue;
				}

				$tmpLink = explode('=', $dl);


				if (isset($tmpLink[1])) {
					if (str_contains($tmpLink[1], 'L3M/')) {
						$tmpLink[1] = substr($tmpLink[1], 4);
					}
					$params[$tmpLink[0]] = $tmpLink[1];
				} else {
					$params[$tmpLink[0]] = '';
				}
			}
		}

		if (isset($params['link'])) {
			return self::decodeLinkEngineVendor($params);
		}


		return $res;
	}

	public static function decodeLinkEngineVendor($params): string
	{
		$res = '';
		$vendor = '';
		$engine = '';

		$link = $params['link'];

		if (isset($params['vendor']) && $params['vendor']) {
			$vendor = $params['vendor'];
		}

		if (isset($params['prodiver']) && $params['prodiver']) {
			$engine = $params['prodiver'];
		}

		if ($vendor === 'amazon') {
			if ($engine === 'tm') {
				$params['link'] = self::decodeLinkTmAmazon($link);
				return $params['link'];
			}
		}

		return $res;
	}

	private static function decodeLinkTmAmazon($link): string
	{
		$res = base64_decode($link);
		$res = urldecode($res);
		$tmp = '';
		if ($res) {
			$pos = strpos($res, '&');
			if ($pos) {
				$res = substr($res, $pos + 1);
			}
			$res = explode('&', $res);
			foreach ($res as $item) {
				$tmp .= '&' . $item;
			}
			return $tmp;
		}

		return $tmp;
	}

	public static function encodeLink($linkUrl, $vendor, $query, $engine): string
	{
		if (!$linkUrl) {
			return '';
		}

		$linkUrl = substr($linkUrl, strlen('&navigation_amz='), strlen($linkUrl));
		$linkUrl = '&vendor=' . $vendor . '&query=' . $query . '&prodiver=' . $engine . '&link=' . $linkUrl;
		return self::codificar($linkUrl . self::getSeed());
	}

	public static function getSeed(): string
	{
		self::$seed = env('ENC_SEED');
		return self::$seed;
	}
}
