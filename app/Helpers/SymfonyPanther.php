<?php

namespace App\Helpers;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\Cookie;

class SymfonyPanther
{
    private static $handles;
    public static function run($url, $userAgent, $cookie)
    {
        $client = Client::createChromeClient(
            null, // Deja la URL de chromedriver por defecto
            [
                $userAgent
            ]
        );

        $client->getCookieJar()->set(new Cookie(
            $cookie
        ));

        $crawler = $client->request('GET', $url);
        sleep(rand(5, 15)); // Esperar entre 5 y 15 segundos
        return true;
    }

    public static function getCookies($url)
    {
        $url = "https://www.amazon.com/Lusso-Gear-Trash-Leakproof-Removable/dp/B079CY4TY3/";
        // Ruta al directorio temporal dentro de `storage`
        $tempDir = storage_path('temp');

        // Crear el directorio si no existe
        if (!file_exists($tempDir)) {
            mkdir($tempDir, 0755, true);
        }

        // Crear un archivo temporal para las cookies
        $cookieFile = tempnam($tempDir, 'cookie_');

        // Verificar si el archivo temporal se creó correctamente
        if ($cookieFile === false) {
            throw new \RuntimeException('No se pudo crear el archivo temporal para cookies.');
        }

        // Lista de proxies
        $proxies = [
            [
                'host' => 'dc.oxylabs.io',
                'port' => 8000,
                'user' => 'user-chupaprecios_lDWEa-country-US',
                'pass' => '+Aq1w2e3r4t5'
            ],
            [
                'host' => 'us-pr.oxylabs.io',
                'port' => 10000,
                'user' => 'customer-chupaprecios_COPc9_K2KrH',
                'pass' => '+Aq1w2e3r4t5'
            ],
            [
                'host' => 'pr.oxylabs.io',
                'port' => 7777,
                'user' => 'customer-jotapey3_qcf4a-cc-us',
                'pass' => '+Aq1w2e3r4t5'
            ],
            [
                'host' => 'pr.oxylabs.io',
                'port' => 7777,
                'user' => 'customer-jotapey2_Kr8Ew-cc-us',
                'pass' => '2H5zdvxVQff'
            ]
        ];

        // Configuración común para todas las solicitudes
        $userAgent = self::getUserAgent();
        $headersParams = [
            'Accept-Encoding: gzip, deflate, br',
            'Accept: */*',
            'Content-Language: es-US',
            'User-Agent: ' . $userAgent,
        ];

        // Función para ejecutar una solicitud multi-cURL


        // Primera solicitud: Obtener cookies iniciales
        $firstResponse = self::executeMultiCurl($url, $proxies, $headersParams, $cookieFile);

        // Extraer cookies de la primera respuesta
        $headerSize = curl_getinfo(self::$handles[0], CURLINFO_HEADER_SIZE);
        $headers = substr($firstResponse, 0, $headerSize);
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
        $cookies = $matches[1];

        // Segunda solicitud: Obtener el $ubid usando las cookies de la primera solicitud
        $secondResponse = self::executeMultiCurl($url, $proxies, $headersParams, $cookieFile, $cookies);

        // Extraer cookies de la segunda respuesta
        $headerSize = curl_getinfo(self::$handles[0], CURLINFO_HEADER_SIZE);
        $headers = substr($secondResponse, 0, $headerSize);
        preg_match_all('/^Set-Cookie:\s*([^;]*)/mi', $headers, $matches);
        $cookies = array_merge($cookies, $matches[1]);

        // Buscar la cookie `ubid-main`
        $ubid = null;
        foreach ($cookies as $cookie) {
            if (strpos($cookie, "ubid-main=") !== false) {
                $ubid = $cookie;
                break;
            }
        }

        if (!$ubid) {
            return false;
        }

        return [
            "cookies" => $cookies,
            "user-agent" => $userAgent,
        ];
    }

    private static function executeMultiCurl($url, $proxies, $headers, $cookieFile, $cookies = [])
    {
        $mh = curl_multi_init();
        self::$handles = [];

        foreach ($proxies as $proxy) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

            // Configurar proxy
            curl_setopt($ch, CURLOPT_PROXY, $proxy['host']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['user'] . ':' . $proxy['pass']);

            // Si hay cookies, agregarlas a la solicitud
            if (!empty($cookies)) {
                curl_setopt($ch, CURLOPT_COOKIE, implode('; ', $cookies));
            }

            curl_multi_add_handle($mh, $ch);
            self::$handles[] = $ch;
        }

        // Ejecutar las solicitudes en paralelo
        $running = null;
        do {
            curl_multi_exec($mh, $running);
            curl_multi_select($mh);
        } while ($running > 0);

        // Recopilar respuestas
        $response = null;
        foreach (self::$handles as $ch) {
            if (curl_errno($ch) === 0) {
                $response = curl_multi_getcontent($ch);
                break; // Usar la primera respuesta exitosa
            }
        }

        // Cerrar todos los manejadores cURL
        foreach (self::$handles as $ch) {
            curl_multi_remove_handle($mh, $ch);
            curl_close($ch);
        }
        curl_multi_close($mh);

        if (!$response) {
            throw new \RuntimeException('Todos los proxies fallaron.');
        }

        return $response;
    }

    public static function getUserAgent(): string
    {
        // Porcentajes aproximados de uso según tendencias actuales (ajustables según estadísticas)
        $deviceWeights = [
            'desktop' => 40, // 40% de conexiones desde escritorio
            'mobile' => 60,  // 60% de conexiones desde móvil
        ];

        $browserWeights = [
            'Chrome' => 65,  // 65% de uso de Chrome
            'Safari' => 20,  // 20% de uso de Safari (principalmente en iOS)
            'Firefox' => 7,  // 7% de uso de Firefox
            'Edge' => 5,     // 5% de uso de Edge
            'Other' => 3,    // 3% otros navegadores
        ];

        // Sistemas operativos de escritorio
        $desktopOs = [
            'Macintosh; Intel Mac OS X 12_1',
            'Macintosh; Intel Mac OS X 11_0',
            'Windows NT 10.0; Win64; x64',
            'Windows NT 11.0; Win64; x64',
        ];

        // Sistemas operativos móviles
        $mobileOs = [
            'Android 13; Mobile',
            'Android 12; Mobile',
            'iPhone; CPU iPhone OS 16_2 like Mac OS X',
            'iPad; CPU OS 16_1 like Mac OS X',
        ];

        $browsers = [
            'Chrome' => [
                'version' => [100, 120], // Versiones de Chrome
                'engine' => 'AppleWebKit/537.36 (KHTML, like Gecko)',
            ],
            'Safari' => [
                'version' => [15, 16], // Versiones de Safari
                'engine' => 'AppleWebKit/605.1.15 (KHTML, like Gecko)',
            ],
            'Firefox' => [
                'version' => [100, 115], // Versiones de Firefox
                'engine' => 'Gecko/20100101 Firefox',
            ],
            'Edge' => [
                'version' => [100, 115], // Versiones de Edge
                'engine' => 'AppleWebKit/537.36 (KHTML, like Gecko) Chrome',
            ],
        ];

        // Selección de dispositivo basada en pesos
        $isMobile = self::weightedRandom($deviceWeights) === 'mobile';
        $os = $isMobile ? $mobileOs[array_rand($mobileOs)] : $desktopOs[array_rand($desktopOs)];

        // Selección de navegador basada en pesos
        $browserKey = self::weightedRandom($browserWeights);
        $browser = $browsers[$browserKey] ?? $browsers['Chrome']; // Predeterminado a Chrome si no se encuentra

        // Generar versión del navegador
        $versionRange = $browser['version'];
        $version = mt_rand($versionRange[0], $versionRange[1]) . '.0.' . mt_rand(1000, 4999);

        $engine = $browser['engine'];

        if ($browserKey === 'Safari') {
            return "Mozilla/5.0 ($os) $engine Version/$version Safari/605.1.15";
        }

        if ($browserKey === 'Firefox') {
            return "Mozilla/5.0 ($os; rv:$version) $engine$version";
        }

        return "Mozilla/5.0 ($os) $engine $browserKey/$version Safari/537.36";
    }

    private static function weightedRandom(array $weights): string
    {
        $totalWeight = array_sum($weights);
        $random = mt_rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($weights as $key => $weight) {
            $currentWeight += $weight;
            if ($random <= $currentWeight) {
                return $key;
            }
        }

        return array_key_first($weights); // Retorna la primera clave como fallback
    }
}
