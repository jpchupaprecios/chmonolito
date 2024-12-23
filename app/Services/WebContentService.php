<?php

declare(strict_types=1);

namespace App\Services;

use App\Services\Interfaces\WebContentInterface;

class WebContentService implements WebContentInterface
{

	public static function scrape(string $url, ?string $cookie = null, bool $clean = true): string|array|false
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
            curl_close($curl);
            if ($response !== false) {

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

	public static function getWebContent($query): mixed {}

    public static function getStrCookie($cookies): string
    {
        if(is_string($cookies)){
            return $cookies;
        }

        $tmpCookies = '';

        if(is_array($cookies)){
            foreach($cookies as $k => $v){
                $tmpCookies .= $k.'='.$v.';';
            }

            return $tmpCookies;
        }

        return $tmpCookies;
    }
	public static function getUserAgent(): string
	{
		$os = [
			'Macintosh; Intel Mac OS X 10_15_7',
			'Macintosh; Intel Mac OS X 10_15_5',
			'Macintosh; Intel Mac OS X 10_11_6',
			'Macintosh; Intel Mac OS X 10_6_6',
			'Macintosh; Intel Mac OS X 10_9_5',
			'Macintosh; Intel Mac OS X 10_10_5',
			'Macintosh; Intel Mac OS X 10_7_5',
			'Macintosh; Intel Mac OS X 10_11_3',
			'Macintosh; Intel Mac OS X 10_10_3',
			'Macintosh; Intel Mac OS X 10_6_8',
			'Macintosh; Intel Mac OS X 10_10_2',
			'Macintosh; Intel Mac OS X 10_10_3',
			'Macintosh; Intel Mac OS X 10_11_5',
			'Windows NT 10.0; Win64; x64',
			'Windows NT 10.0; WOW64',
			'Windows NT 10.0',
		];

		$randomOs = $os[array_rand($os)];
		$randomChromeVersion = mt_rand(85, 87) . '.0.' . (mt_rand(4100, 4290)) . '.' . (mt_rand(140, 189));

		return "Mozilla/5.0 ($randomOs) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/$randomChromeVersion Safari/537.36";
	}

	public static function parseUrlQuery($url): array
	{
		$result = [];
		if ($url) {
			return parse_url($url, PHP_URL_QUERY);
		}
		return $result;
	}

    /**
     * Obtiene las cabeceras de la solicitud cURL.
     * @param string|null $cookie
     * @return array
     */
    public static function getHeaders(?string $cookie): array
	{
		$headers = [
			'Accept-Encoding: gzip, deflate, br',
			'Connection: keep-alive',
			'Accept: */*',
			'Content-Language: es-US',
			'User-Agent: ' . self::getUserAgent(),
			'Cookie: ' . self::getStrCookie($cookie),
		];

		if (mt_rand(0, 1)) {
			$headers[] = 'Downlink: ' . mt_rand(10, 40);
		}

		if (mt_rand(0, 1)) {
			$headers[] = 'Rtt: ' . mt_rand(50, 149);
		}

		if (mt_rand(0, 1)) {
			$headers[] = 'Pragma: no-cache';
		}

		if (mt_rand(0, 1)) {
			$headers[] = 'Ect: 4g';
		}

		if (mt_rand(0, 1)) {
			$headers[] = 'DNT: 1';
		}

		return $headers;
	}

	public static function getChapiCookie(array $awsData): string
	{
		if (!isset($awsData['session_id']) || !isset($awsData['session_time_id']) || !isset($awsData['ubid_main'])) {
			return '';
		}

		$cookie = 'session-id=' . $awsData['session_id'] . ';session-id-time=' . $awsData['session_time_id'] . ';ubid-main=' . $awsData['ubid_main'];
		return $cookie;
	}
}
