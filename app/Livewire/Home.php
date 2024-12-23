<?php

namespace App\Livewire;

use Livewire\Component;

class Home extends Component
{
    public string $query = '';
    public int $page = 1;
    public string $vendor = 'amazon';
    public string $engineId = 'direct';
    protected const COOKIE_PATH = 'app/';

    public function search()
    {
        // Habilita la salida inmediata
        if (ob_get_level()) {
            ob_end_clean();
        }
        ob_implicit_flush(true);

        $url = 'https://www.amazon.com/s?k=iphone&language=es_US&page=1';
        $cookie = '...'; // Tu cookie

        $curl = curl_init($url);

        curl_setopt_array($curl, [
            CURLOPT_HTTPHEADER => self::getHeaders($cookie),
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_RETURNTRANSFER => false,
            CURLOPT_COOKIE => $cookie,
            CURLOPT_USERAGENT => self::getUserAgent(),
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) {
                echo $chunk;
                flush();
                return strlen($chunk);
            },
        ]);

        curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Error: ' . curl_error($curl);
        }

        curl_close($curl);
    }


    public function render()
    {
        return view('livewire.home')->layout('layouts.guest');
    }

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
}
