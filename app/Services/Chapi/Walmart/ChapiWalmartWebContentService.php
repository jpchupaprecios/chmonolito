<?php

namespace App\Services\Chapi\Walmart;


use App\Services\WebContentService;
use App\Models\Cookie;
class ChapiWalmartWebContentService extends WebContentService
{
    protected static string $seed;

    public static function scrape($url, $cookies = null, $clean = true)
    {
        $headers = [
            'Connection: keep-alive',
            'Accept: */*',
            'Content-Language: es-US',
            'Accept-Encoding: gzip, deflate, br',
            'User-Agent: ' . self::getUserAgent(),
            'Cookie: ' . self::getStrCookie($cookies),
        ];

        $proxyHost = env('OXYLABS_PROXY_MX');
        $proxyPort = env('OXULABS_PORT');
        $proxyUser = env('OXYLABS_USER_MX');
        $proxyPass = env('OXYLABS_PASS');

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        if ($cookies) {
            $cookieName = date('Y-m-d') . 'walmart';
            curl_setopt($curl, CURLOPT_COOKIEFILE, storage_path('app/' . $cookieName . '.txt'));
            curl_setopt($curl, CURLOPT_COOKIEJAR, storage_path('app/' . $cookieName . '.txt'));
        }

        curl_setopt($curl, CURLOPT_PROXY, $proxyHost);
        curl_setopt($curl, CURLOPT_PROXYPORT, $proxyPort);
        curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxyUser . ':' . $proxyPass);

        //customer-jotapey:g4cir9dx2ghmdI2@mx-pr.oxylabs.io:10000
        $response = curl_exec($curl);
        $curlError = curl_error($curl);

        curl_close($curl);

        if ($response === false) {
            // Manejar el error de cURL
            throw new \Exception('Curl error: ' . $curlError);
        }

        $decodedResponse = @gzdecode($response);
        if ($decodedResponse === false) {
            // Si la decodificación falla, retorna la respuesta original
            return $response;
        }

        return $decodedResponse;
    }

    private static function decodeChapiDirectCall($url, $response, $tmpResponse, $clean = true)
    {
        if(self::is_gzipped($response)){
            $response = @gzdecode($response);
        }

        if (!$response) {
            $response = $tmpResponse;
        }

        return $response;
    }

    private static function is_gzipped($data) {
        // Verifica si el primer y segundo byte corresponden al encabezado de gzip
        return (substr($data, 0, 2) === "\x1F\x8B");
    }

}
