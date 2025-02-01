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
        $headers = [
            'Connection: keep-alive',
            'Accept: */*',
            'Content-Language: es-US',
            'User-Agent: ' . self::getUserAgent(),
        ];

        $proxies = [
            [
                'host' => 'dc.oxylabs.io',
                'port' => 8000,
                'user' => 'user-chupaprecios_lDWEa-country-US',
                'pass' => '+Aq1w2e3r4t5'
            ],
            [
                'host' => 'pr.oxylabs.io',
                'port' => 7777,
                'user' => 'customer-jotapey3_qcf4a-cc-us',
                'pass' => '+Aq1w2e3r4t5'
            ],
        ];

        $multiCurl = curl_multi_init();
        $handles = [];
        $winnerHandle = null;
        $firstValidResponse = false;
        $response = false;

        foreach ($proxies as $proxy) {
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            if ($cookie) {
                curl_setopt($curl, CURLOPT_HTTPHEADER, self::getHeaders($cookie, $userAgent));
            }

            // Configurar proxy
            curl_setopt($curl, CURLOPT_PROXY, $proxy['host']);
            curl_setopt($curl, CURLOPT_PROXYPORT, $proxy['port']);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy['user'] . ':' . $proxy['pass']);

            // Agregar el handle al multi-cURL
            curl_multi_add_handle($multiCurl, $curl);
            $handles[] = $curl;
        }

        // Ejecutar las solicitudes en paralelo
        do {
            $status = curl_multi_exec($multiCurl, $active);
            curl_multi_select($multiCurl, 0.2); // Pequeño timeout para no bloquear

            // Verificar si alguna solicitud ha terminado
            while ($info = curl_multi_info_read($multiCurl)) {
                $handle = $info['handle'];
                $result = curl_multi_getcontent($handle);

                // Verificar si la respuesta está bloqueada
                $decodedResponse = self::decodeChapiDirectCall($url, $result, $clean);

                if ($decodedResponse['response'] !== 'unauthorized' && !$firstValidResponse) {
                    $firstValidResponse = true;
                    $winnerHandle = $handle;
                    $response = $decodedResponse['response'];
                    if($response){
                        // Cerrar todos los handles de cURL antes de retornar
                        foreach ($handles as $curlHandle) {
                            if ($curlHandle !== $winnerHandle) {
                                curl_multi_remove_handle($multiCurl, $curlHandle);
                                curl_close($curlHandle);
                            }
                        }

                        // Cerrar el handle ganador
                        curl_multi_remove_handle($multiCurl, $winnerHandle);
                        curl_close($winnerHandle);

                        // Cerrar el multi-cURL
                        curl_multi_close($multiCurl);

                        return $response;
                    }
                }

                // Cerrar los handles que no sean el ganador
                if ($handle !== $winnerHandle) {
                    curl_multi_remove_handle($multiCurl, $handle);
                    curl_close($handle);
                }
            }
        } while ($active && $status == CURLM_OK);

        // Cerrar todos los handles restantes
        foreach ($handles as $handle) {
            if ($handle !== $winnerHandle) {
                curl_multi_remove_handle($multiCurl, $handle);
                curl_close($handle);
            }
        }

        // Cerrar el multi-cURL
        curl_multi_close($multiCurl);

        // Procesar la respuesta
        if ($response !== false) {
            return $response;
        }

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
            return ['response' => $cleanResponse];
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
