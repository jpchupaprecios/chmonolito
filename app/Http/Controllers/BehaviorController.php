<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
use App\Helpers\SymfonyPanther;
use App\Models\ScrapingSession;
class BehaviorController extends Controller
{
    public function index($client_session_id, $url = null)
    {

        if(!$url){
            $url = 'https://www.amazon.com/';
        }

        if(strpos($url, '=') !== false){
            $tmp = explode('=', $url);
            if($tmp[0] == 'p'){
                $url = 'https://www.amazon.com/dp/' . $tmp[1];
            }elseif($tmp[0] == 'q'){
                $url = "https://www.amazon.com/s?k=" . urlencode($tmp[1]) . "&language=es_US&page=1";
            }
        }

        $scrapingSession = null;
        if($client_session_id){
            $scrapingSession = ScrapingSession::where("client_session_id", $client_session_id)->first();
        }

        if(!$scrapingSession){
            $scrapingSession = new ScrapingSession();
            $cookies = SymfonyPanther::getCookies($url);
            if(!$cookies){
                return false;
            }
            $userAgent = $cookies["user-agent"];
            $cookies = $cookies["cookies"];


            $scrapingSession->client_session_id = $client_session_id;
            $cookieStr = "";
            foreach($cookies as $cookie){
                $cookieStr .= $cookie . ";";
            }
            $scrapingSession->amazon_cookie = $cookieStr;
            $scrapingSession->user_agent = $userAgent;
            $scrapingSession->save();

            return true;
        }

        return response()->json([
            'message' => 'Error starting scraping session'
        ], 500);
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
