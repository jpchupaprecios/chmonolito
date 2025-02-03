<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
class ProductController extends Controller
{

    public function checkMultiProxy(Request $request, $id){
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
            // Puedes incluir más proxies...
        ];

        $url = 'https://www.amazon.com/dp/' . $id; // La URL que quieres probar
        $proxyTimings = [];

        foreach ($proxies as $proxy) {
            $ch = curl_init($url);

            // Configuración básica para medir el tiempo de respuesta
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10); // Tiempo máximo para la ejecución
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5); // Tiempo máximo para conectar
            curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

            // Configuración del proxy
            curl_setopt($ch, CURLOPT_PROXY, $proxy['host']);
            curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
            curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['user'] . ':' . $proxy['pass']);

            // Opcional: Puedes usar una solicitud HEAD para obtener solo la cabecera y reducir el tiempo
            curl_setopt($ch, CURLOPT_NOBODY, true);

            $start = microtime(true);
            $response = curl_exec($ch);
            $timeTaken = microtime(true) - $start;

            $error = curl_error($ch);
            curl_close($ch);

            $proxyTimings[] = [
                'proxy' => $proxy['host'] . ':' . $proxy['port'],
                'time'  => $timeTaken,
                'error' => $error
            ];
        }

        // Ordenamos los proxies por tiempo de respuesta (latencia)
        usort($proxyTimings, function($a, $b) {
            return $a['time'] <=> $b['time'];
        });

        // Imprimimos los resultados
        echo "<pre>";
        print_r($proxyTimings);
        echo "</pre>";
    }

    public function checkSingleProxy(Request $request, $id)
    {
        // Definimos el proxy a utilizar: el que está en el puerto 8000
        $proxy = [
            'host' => 'dc.oxylabs.io',
            'port' => 8000,
            'user' => 'user-chupaprecios_lDWEa-country-US',
            'pass' => '+Aq1w2e3r4t5'
        ];

        // La URL que queremos probar
        $url = 'https://www.amazon.com/dp/' . $id;

        // Iniciamos cURL
        $ch = curl_init($url);

        // Configuración básica para medir el tiempo de respuesta
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);          // Tiempo máximo para la ejecución
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);      // Tiempo máximo para conectar
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);

        // Configuración del proxy
        curl_setopt($ch, CURLOPT_PROXY, $proxy['host']);
        curl_setopt($ch, CURLOPT_PROXYPORT, $proxy['port']);
        curl_setopt($ch, CURLOPT_PROXYUSERPWD, $proxy['user'] . ':' . $proxy['pass']);

        // Usamos una solicitud HEAD para obtener solo la cabecera y reducir el tiempo
        curl_setopt($ch, CURLOPT_NOBODY, true);

        // Medir el tiempo de ejecución
        $start = microtime(true);
        $response = curl_exec($ch);
        $timeTaken = microtime(true) - $start;

        $error = curl_error($ch);
        curl_close($ch);

        // Imprimimos los resultados
        echo "<pre>";
        echo "Proxy: " . $proxy['host'] . ":" . $proxy['port'] . "\n";
        echo "Tiempo de respuesta: " . $timeTaken . " segundos\n";
        if ($error) {
            echo "Error: " . $error . "\n";
        }
        echo "</pre>";
    }

}
