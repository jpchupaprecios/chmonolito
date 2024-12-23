<?php

declare(strict_types=1);

namespace App\Services\Chapi\Amazon;

use App\Services\WebContentService;
use Exception;
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
    public static function scrape(string $url, ?string $cookie = null, bool $clean = true): string|array|false
    {
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');

        // Habilitar flush inmediato.
        ob_implicit_flush(true);
        ob_end_flush();

        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => self::getHeaders($cookie),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => false, // Deshabilitar retorno automático.
            CURLOPT_COOKIEFILE => $cookiePath,
            CURLOPT_COOKIEJAR => $cookiePath,
            CURLOPT_USERAGENT => self::getUserAgent(),
            CURLOPT_TIMEOUT => 30,
            CURLOPT_CONNECTTIMEOUT => 5,
            CURLOPT_ENCODING => '',
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) {
                echo $chunk; // Envía cada chunk directamente al cliente.
                flush();     // Asegúrate de que se envía al navegador.
                return strlen($chunk);
            },
        ]);

        curl_exec($curl);
        $error = curl_error($curl);

        if (!empty($error)) {
            curl_close($curl);
            throw new Exception("Curl error: $error");
        }

        curl_close($curl);
        return true;
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
