<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonProductParser;
use Illuminate\Support\Facades\File;

class ScrapingProductController extends Controller
{
    protected const COOKIE_PATH = 'app/';


    public function product(Request $request, $productId, $vendor)
    {
        //\App\Models\ChunkProductLog::truncate();
        //return $this->productDebug($request, $productId, $vendor);
        $url = 'https://www.amazon.com/dp/' . $productId;
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'session-id=145-2848617-2390738; i18n-prefs=USD; skin=noskin; ubid-main=134-0166731-7376829; lc-main=es_US; session-id-time=2082787201l; aws-mkto-trk=id%3A112-TZM-766%26token%3A_mch-aws.amazon.com-1714592830565-97216; aws_lang=en; AMCVS_7742037254C95E840A4C98A6%40AdobeOrg=1; s_cc=true; aws-target-visitor-id=1731609730515-285914.44_0; remember-account=false; regStatus=registered; aws-account-alias=chupaprecios; aws-target-data=%7B%22support%22%3A%221%22%7D; x-main=cU7cDGCKZ8IpQIFcR7d0MFhw2ZDY@XWQwHGhPVLILRsw2?9FRuYU?@Rl5rgvnkaS; AMCV_7742037254C95E840A4C98A6%40AdobeOrg=1585540135%7CMCIDTS%7C20069%7CMCMID%7C64029980295631160031792846785696741143%7CMCAAMLH-1734547835%7C4%7CMCAAMB-1734547835%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1733950235s%7CNONE%7CMCAID%7CNONE%7CMCSYNCSOP%7C411-20071%7CvVersion%7C4.4.0; aws-userInfo=%7B%22arn%22%3A%22arn%3Aaws%3Aiam%3A%3A250933440275%3Auser%2Fjotapey%22%2C%22alias%22%3A%22chupaprecios%22%2C%22username%22%3A%22jotapey%22%2C%22keybase%22%3A%22%22%2C%22issuer%22%3A%22http%3A%2F%2Fsignin.aws.amazon.com%2Fsignin%22%2C%22signinType%22%3A%22PUBLIC%22%7D; session-token=/tJ+/CCt3ecWOLDRhCG/tL8xrdIkyi2Bd5fsikNKnANA+8voKSaAeXwFQivieZO8MBe86nF3tV7tR0VX9pEhifgTLs0jr5t1fXW1nNQ/CPIrpUR7xm7EMO6EiraGX1pLa/6vTltsIDOotD331zIeTwNugbuh6jjtEfFo84BNqJeGas4qighci3peTngb/fiMy/qfX8RL6dFyM0Ilz7mokL30aoEH7vQnG7bJvkFZqqNJyUl1sEqDrzpO4QzD8Xm965+zr+F5DHHtIsGqtOS60TTuTctJCugnvIljH1nLMKqxmHXlg4rvvj2l+glc8kKbKXK/9Yaj7JfUIKQAoahiPGVHThDY8LM8mClX1pCOTi/mrkddBKKeu7qtojwPMjVz; csm-hit=tb:s-HWT4HN3DM66R21GM69TK|1734878749583&t:1734878751211&adb:adblk_no; amp_389c1b=3961e650-93a6-45e8-9540-eca24b1e2495...1ifncffmi.1ifnd9r6u.0.0.0';

        // Configurar streaming y encabezados
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        $datas = [
            "title" => [
                "status" => "pending",
                "content" => ""
            ],
            "price" => [
                "status" => "pending",
                "content" => ""
            ],
            "image" => [
                "status" => "pending",
                "content" => ""
            ],
            "variant" => [
                "status" => "pending",
                "content" => ""
            ],
            "variant_color" => [
                "status" => "pending",
                "content" => ""
            ],
        ];

        // Enviar HTML inicial
        //$html = file_get_contents(public_path('templates/product.html'));
        //die($html);
        // Asegurarse de enviar los datos al cliente
        flush();

        // Añadir un padding para evitar buffering
        echo str_repeat(" ", 1024);
        flush();

        // Configurar cURL
        $curl = curl_init($url);
        $proxyHost = env('OXYLABS_PROXY');
        $proxyPort = env('OXULABS_PORT');
        $proxyUser = env('OXYLABS_USER_US');
        $proxyPass = env('OXYLABS_PASS');
        $useProxy = env('USE_PROXY');

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
            CURLOPT_BUFFERSIZE => 1024, // Reduce el tamaño del buffer de cURL
            CURLOPT_PROXY => $proxyHost, // Proxy host
            CURLOPT_PROXYPORT => $proxyPort, // Proxy port
            CURLOPT_PROXYUSERPWD => $proxyUser . ':' . $proxyPass, // Proxy authentication
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) use (&$buffer, &$datas, $productId) {
                $result = AmazonProductParser::processHtmlChunks($chunk, $buffer, $datas, $productId);
                if ($result) {
                    foreach ($result as $key => $content) {
                        echo "<div class='result'>{$content}</div>";
                        flush();
                        return strlen($chunk);
                    }
                }

            },
        ]);

        curl_exec($curl);

        if (curl_errno($curl)) {
            //echo "<p>Error: " . curl_error($curl) . "</p>";
        }

        curl_close($curl);

        // Finalizar la página HTML
        echo "</div></body></html>";
        flush(); // Asegurarse de enviar el contenido final
    }




    private function getHeaders($cookie): array
    {
        return [
            'Accept-Encoding: gzip, deflate, br',
            'Connection: keep-alive',
            'Accept: */*',
            'Content-Language: es-US',
            'User-Agent: ' . $this->getUserAgent(),
            'Cookie: ' . $cookie,
        ];
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
}
