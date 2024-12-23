<?php

declare(strict_types=1);

namespace App\Services\Chapi\Ebay;

use App\Services\WebContentService;

final class ChapiEbayWebContentService extends WebContentService
{
	protected static string $seed;

	public static function scrape($url, $cookie = null, $clean = true)
	{
		$headers = [
			'Connection: keep-alive',
			'Accept: */*',
			'Content-Language: es-US',
			'User-Agent: ' . self::getUserAgent(),
		];

		$curl = curl_init($url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

		if ($cookie) {
			curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders($cookie));
		}

		$response = curl_exec($curl);
		try {
			if ($response === false) {
				$error = curl_error($curl);
				curl_close($curl);
			} else {
				curl_close($curl);
				$tmpResponse = $response;

				if ($cookie) {
					return self::decodeChapiDirectCall($url, $response, $tmpResponse, $clean);
				}

				$body = preg_replace('/\s\s+/', '', $response);
				$body = preg_replace('/\n/', '', $body);

				if ($body) {
					return $body;
				}

				return false;
			}
		} catch (Exception $e) {
			curl_close($curl);
			return false;
		}

		curl_close($curl);
		return false;
	}

	public static function getWebContent($query): mixed
	{
		$queryString = http_build_query([
			'api_key' => env('RAINFOREST_API_KEY'),
			'type' => 'search',
			'amazon_domain' => 'amazon.com',
			'search_term' => $query,
		]);

		$ch = curl_init(sprintf('%s?%s', 'https://api.rainforestapi.com/request', $queryString));
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

		curl_setopt($ch, CURLOPT_TIMEOUT, 180);

		$api_result = curl_exec($ch);
		curl_close($ch);

		if ($api_result) {
			return @json_decode($api_result, true);
		}
		return false;
	}

	private static function decodeChapiDirectCall($url, $response, $tmpResponse, $clean = true)
	{
		$response = @gzdecode($response);

		if (!$response) {
			$response = $tmpResponse;
		}

		return $response;
	}
}
