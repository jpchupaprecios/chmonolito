<?php

declare(strict_types=1);

namespace App\Services\Chapi\Amazon;

use App\Services\WebContentService;
use Exception;
use Illuminate\Support\Facades\Log;

final class ChapiAmazonWebContentService extends WebContentService
{
    /**
     *
     */
    protected const COOKIE_PATH = 'app/';


    /**
     * Realiza scraping de contenido web.
     * @param string $url
     * @param string|null $cookie
     * @param bool $clean
     * @return string|array|false
     * @throws Exception
     */
    public static function scrape($url, $cookie = null, $userAgent = "", $clean = true, $debug = false): false|array|string
    {
        $debug = false;//self::getDebug($debug);

        $debugging = [];
        if ($debug) {
            $debugging['methods']['scrape']['benckmark']['start_scraping'] = microtime(true);
            $debugging['methods']['scrape']['path'] = 'app/Services/Chapi/Amazon/ChapiAmazonWebContentService';
        }

        /*$proxyHost = env('OXYLABS_PROXY');
        $proxyPort = env('OXULABS_PORT');
        $proxyUser = env('OXYLABS_USER_US');
        $proxyPass = env('OXYLABS_PASS');*/

        if(!$userAgent){
            $userAgent = self::getUserAgent();
        }
        $headers = [
            'Connection: keep-alive',
            'Accept: */*',
            'Content-Language: es-US',
            'User-Agent: ' . $userAgent,
        ];

        $proxyUrl = env('OXYLABS_PROXY_URL');


        /**/
        $proxyHost = env('OXYLABS_PROXY');
        $proxyPort = env('OXULABS_PORT');
        $proxyUser = env('OXYLABS_USER_US');
        $proxyPass = env('OXYLABS_PASS');
        $useProxy = env('USE_PROXY');

        /**/

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders($cookie, $userAgent));

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $cookieName = date('Y-m-d') . 'amazon';
        curl_setopt($curl, CURLOPT_COOKIEFILE, storage_path('app/' . $cookieName . '.txt'));
        curl_setopt($curl, CURLOPT_COOKIEJAR, storage_path('app/' . $cookieName . '.txt'));

        if($useProxy){
            curl_setopt($curl, CURLOPT_PROXY, $proxyHost);
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxyPort);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyUser . ':' . $proxyPass);
        }

        $response = curl_exec($curl);
        if ($debug) {
            $debugging['methods']['scrape']['benckmark']['end_scraping'] = microtime(true);
            $debugging['methods']['scrape']['benckmark']['scraping_result'] = $debugging['methods']['scrape']['benckmark']['end_scraping'] - $debugging['methods']['scrape']['benckmark']['start_scraping'];
        }
        $res = [];
        try {
            if ($response === false) {
                $useProxy = env('USE_PROXY');
                $debugging['methods']['scrape']['reintento'] = true;
                $error = curl_error($curl);
                curl_close($curl);

                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders($cookie));

                if($useProxy){
                    curl_setopt($curl, CURLOPT_PROXY, $proxyUrl);
                }

                $response = curl_exec($curl);
                curl_close($curl);
            }

            if ($response === false) {
                Log::debug('response equals false');
                $debugging['methods']['scrape']['falla scraping'] = true;
                $error = curl_error($curl);
                curl_close($curl);
                if ($debug) {
                    $res['debugging'] = $debugging;
                }
                return $res;
            }
            curl_close($curl);
            $tmpResponse = $response;


            if ($debug) {
                $debugging['methods']['scrape']['benckmark']['start_global_decode'] = microtime(true);
            }
            $result = self::decodeChapiDirectCall($url, $response, $clean);

            $res['result'] = $result['response'];
            if ($debug) {
                $debugging = array_merge($debugging, $result['debbuging']);
                $debugging['methods']['scrape']['benckmark']['end_global_decode'] = microtime(true);
                $debugging['methods']['scrape']['benckmark']['global_decode_result'] = $debugging['methods']['scrape']['benckmark']['end_global_decode'] - $debugging['methods']['scrape']['benckmark']['start_global_decode'];
            }


            if ($debug) {
                $res['debugging'] = $debugging;
            }

            if(isset($res["result"])){
                $res = $res["result"];
            }

            if(is_string($res)){
                $body = preg_replace('/\s\s+/', '', $res);
                $body = preg_replace('/\n/', '', $body);
                Log::debug('Response return body');
                return $body;
            }

            if(is_array($res)){
                Log::debug('Response return array');
                return $res;
            }
            Log::debug('Response return false');
            return false;
        } catch (Exception $e) {
            curl_close($curl);
            return false;
        }

        curl_close($curl);
        return false;
    }

    /**
     * Procesa la respuesta de la solicitud cURL.
     * @param string $url
     * @param string $response
     * @param bool $clean
     * @return string|array|false
     */
    private static function processResponse(string $url, string $response, bool $clean): string|array|false
    {
        $decodedResponse = self::decodeChapiDirectCall($url, $response, $clean);

        if (isset($decodedResponse['response'])) {
            return $decodedResponse['response'];
        }

        return false;
    }

    /**
     * Decodifica la respuesta de la solicitud cURL.
     * @param string $url
     * @param string $response
     * @param bool $clean
     * @return array
     */
    private static function decodeChapiDirectCall(string $url, string $response, bool $clean): array
    {
        $response = @gzdecode($response) ?: $response;

        if (str_contains($response, 'item cannot be shipped')) {
            return ['response' => 'unauthorized'];
        }

        if (str_contains($response, 'To discuss automated')) {
            $response = @file_get_contents($url) ?: '';
            if (str_contains($response, 'To discuss automated')) {
                return ['response' => 'unauthorized'];
            }
        }

        if ($clean) {
            $cleanResponse = str_replace(["\r", "\n", '&&&'], ['', '', ','], $response);

            $ret['response'] = $cleanResponse;
            return $ret;
        }

        return ['response' => $response];
    }

    /**
     * Obtiene los datos de la cookie de Amazon.
     * @param array $awsData
     * @return string
     */
    public static function getChapiCookie(array $awsData): string
    {
        if (
            empty($awsData['session_id']) ||
            empty($awsData['session_time_id']) ||
            empty($awsData['ubid_main'])
        ) {
            return '';
        }

        return sprintf(
            'session-id=%s; session-id-time=%s; ubid-main=%s',
            $awsData['session_id'],
            $awsData['session_time_id'],
            $awsData['ubid_main']
        );
    }

}
