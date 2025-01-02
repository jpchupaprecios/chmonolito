<?php

namespace App\Http\Controllers;

use App\Models\ProductTry;
use Illuminate\Http\Request;
use App\Helpers\AmazonProductParserV1;
use Illuminate\Support\Facades\File;

class ScrapingProductControllerV1 extends Controller
{
    protected const COOKIE_PATH = 'app/';

    public function productDebug(Request $request, $productId, $vendor)
    {
        $url = 'https://www.amazon.com/dp/' . $productId;
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'session-id=145-2848617-2390738; i18n-prefs=USD; skin=noskin; ubid-main=134-0166731-7376829; lc-main=es_US; session-id-time=2082787201l; aws-mkto-trk=id%3A112-TZM-766%26token%3A_mch-aws.amazon.com-1714592830565-97216; aws_lang=en; AMCVS_7742037254C95E840A4C98A6%40AdobeOrg=1; s_cc=true; aws-target-visitor-id=1731609730515-285914.44_0; remember-account=false; regStatus=registered; aws-account-alias=chupaprecios; AMCV_7742037254C95E840A4C98A6%40AdobeOrg=1585540135%7CMCIDTS%7C20042%7CMCMID%7C64029980295631160031792846785696741143%7CMCAAMLH-1732297343%7C4%7CMCAAMB-1732297343%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1731699743s%7CNONE%7CMCAID%7CNONE%7CMCSYNCSOP%7C411-20049%7CvVersion%7C4.4.0; aws-target-data=%7B%22support%22%3A%221%22%7D; aws-userInfo=%7B%22arn%22%3A%22arn%3Aaws%3Aiam%3A%3A250933440275%3Auser%2Fjotapey%22%2C%22alias%22%3A%22chupaprecios%22%2C%22username%22%3A%22jotapey%22%2C%22keybase%22%3A%22G3w6E3KvhbSjSOSJTD%2BN1RDpvnFhY5fEwylvZMFtDTE%5Cu003d%22%2C%22issuer%22%3A%22http%3A%2F%2Fsignin.aws.amazon.com%2Fsignin%22%2C%22signinType%22%3A%22PUBLIC%22%7D; aws-userInfo-signed=eyJ0eXAiOiJKV1MiLCJrZXlSZWdpb24iOiJ1cy1lYXN0LTIiLCJhbGciOiJFUzM4NCIsImtpZCI6IjkzNTA3OGZiLTY2NzYtNDlhMC1iN2YxLTBjZDAzOTBkMDNjMCJ9.eyJzdWIiOiJjaHVwYXByZWNpb3MiLCJzaWduaW5UeXBlIjoiUFVCTElDIiwiaXNzIjoiaHR0cDpcL1wvc2lnbmluLmF3cy5hbWF6b24uY29tXC9zaWduaW4iLCJrZXliYXNlIjoiRzN3NkUzS3ZoYlNqU09TSlREK04xUkRwdm5GaFk1ZkV3eWx2Wk1GdERURT0iLCJhcm4iOiJhcm46YXdzOmlhbTo6MjUwOTMzNDQwMjc1OnVzZXJcL2pvdGFwZXkiLCJ1c2VybmFtZSI6ImpvdGFwZXkifQ.COQzljVSE_9ggEu2PQbMZJQR5OCNRSXT0PWP0ZlOirECgTzp-GdNYajBYluBOstpCgytoq5Cs-ZyJGoRlWdDqYQmNcCFbl0BjoVuneQHhHLPCRzmiRsk1y36AgHbvWCZ; x-main=cU7cDGCKZ8IpQIFcR7d0MFhw2ZDY@XWQwHGhPVLILRsw2?9FRuYU?@Rl5rgvnkaS; session-token=gAAKgJuzsLWFJX/xG5a0Ms9hhrEBwNVRw65/DSPbjfYpeSWYpO8hll2OE2zhcbPz7yZXSk3wbCTx29AQ34hB+PjmUFcLwjNPc5MAT3IiSlGX0C1dgJJBX9FOHzWfJrgi/VqaFg/KW0DT2TV/SfSse8hGCduO2TOKHSnAnD71K7sIgs2+hYhfnoJCMl+0g+PAVYUAi65eV789GtofaOQorlqQjDWZ5bmIWox2USWwNvMWkxTUKAm8AaKp/Jc/Xotc5Z5ZUbDTLpEHlbpQNbHSnqeab1d75klibKDDNPOSkz1I/Pr/SZjMG1dIA7ezlJ9ZIp84R8iqDqNPIJtj5TiOpN4X02JDKW5EfsXVzwRgiNz0KaFQ0E5HKH+nMYyYk6lc; amp_389c1b=3961e650-93a6-45e8-9540-eca24b1e2495...1idooa3mq.1idooc2g7.0.0.0; csm-hit=tb:RCSB4XGBD2RWHZ4Q61N2+s-RCSB4XGBD2RWHZ4Q61N2|1732776435511&t:1732776435511&adb:adblk_no';

        // Configurar streaming y encabezados
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        flush();

        // Configurar cURL
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
            CURLOPT_BUFFERSIZE => 1024, // Reduce el tamaño del buffer de cURL
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) {
                echo $chunk;
                flush(); // Enviar el chunk al navegador
                return strlen($chunk);
            },
        ]);

        curl_exec($curl);

        if (curl_errno($curl)) {
            echo "<p>Error: " . curl_error($curl) . "</p>";
        }

        curl_close($curl);

        flush(); // Asegurarse de enviar el contenido final
    }

    private static function deleteHtmlFiles($folderName)
    {
        // Ruta completa dentro de storage
        $path = storage_path("app/{$folderName}");

        // Verifica si la carpeta existe
        if (File::exists($path)) {
            // Elimina todos los archivos y subdirectorios dentro de la carpeta
            File::deleteDirectory($path);
            return true;
        }

        return false; // Retorna false si la carpeta no existe
    }
    public function product(Request $request, $productId, $vendor)
    {
        $productTry = ProductTry::where('sku', $productId)->first();
        if(!$productTry){
            $try = 1;
        }else{
            $try = $productTry->count + 1;
        }
        ProductTry::updateOrCreate(
            ['sku' => $productId],
            ['count' => $try]
        );
        self::deleteHtmlFiles($productId);
        //\App\Models\ChunkProductLog::truncate();
        //return $this->productDebug($request, $productId, $vendor);
        $url = 'https://www.amazon.com/dp/' . $productId;
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'session-id=145-2848617-2390738; i18n-prefs=USD; skin=noskin; ubid-main=134-0166731-7376829; lc-main=es_US; session-id-time=2082787201l; aws-mkto-trk=id%3A112-TZM-766%26token%3A_mch-aws.amazon.com-1714592830565-97216; aws_lang=en; AMCVS_7742037254C95E840A4C98A6%40AdobeOrg=1; s_cc=true; aws-target-visitor-id=1731609730515-285914.44_0; remember-account=false; regStatus=registered; aws-account-alias=chupaprecios; AMCV_7742037254C95E840A4C98A6%40AdobeOrg=1585540135%7CMCIDTS%7C20042%7CMCMID%7C64029980295631160031792846785696741143%7CMCAAMLH-1732297343%7C4%7CMCAAMB-1732297343%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1731699743s%7CNONE%7CMCAID%7CNONE%7CMCSYNCSOP%7C411-20049%7CvVersion%7C4.4.0; aws-target-data=%7B%22support%22%3A%221%22%7D; aws-userInfo=%7B%22arn%22%3A%22arn%3Aaws%3Aiam%3A%3A250933440275%3Auser%2Fjotapey%22%2C%22alias%22%3A%22chupaprecios%22%2C%22username%22%3A%22jotapey%22%2C%22keybase%22%3A%22G3w6E3KvhbSjSOSJTD%2BN1RDpvnFhY5fEwylvZMFtDTE%5Cu003d%22%2C%22issuer%22%3A%22http%3A%2F%2Fsignin.aws.amazon.com%2Fsignin%22%2C%22signinType%22%3A%22PUBLIC%22%7D; aws-userInfo-signed=eyJ0eXAiOiJKV1MiLCJrZXlSZWdpb24iOiJ1cy1lYXN0LTIiLCJhbGciOiJFUzM4NCIsImtpZCI6IjkzNTA3OGZiLTY2NzYtNDlhMC1iN2YxLTBjZDAzOTBkMDNjMCJ9.eyJzdWIiOiJjaHVwYXByZWNpb3MiLCJzaWduaW5UeXBlIjoiUFVCTElDIiwiaXNzIjoiaHR0cDpcL1wvc2lnbmluLmF3cy5hbWF6b24uY29tXC9zaWduaW4iLCJrZXliYXNlIjoiRzN3NkUzS3ZoYlNqU09TSlREK04xUkRwdm5GaFk1ZkV3eWx2Wk1GdERURT0iLCJhcm4iOiJhcm46YXdzOmlhbTo6MjUwOTMzNDQwMjc1OnVzZXJcL2pvdGFwZXkiLCJ1c2VybmFtZSI6ImpvdGFwZXkifQ.COQzljVSE_9ggEu2PQbMZJQR5OCNRSXT0PWP0ZlOirECgTzp-GdNYajBYluBOstpCgytoq5Cs-ZyJGoRlWdDqYQmNcCFbl0BjoVuneQHhHLPCRzmiRsk1y36AgHbvWCZ; x-main=cU7cDGCKZ8IpQIFcR7d0MFhw2ZDY@XWQwHGhPVLILRsw2?9FRuYU?@Rl5rgvnkaS; session-token=OT379JmI6GzVNkhh0gF0S0l7dRyQCcHFyQ+dqhie7I/ouZ3Bf1LY0wpOB6/3m6rGnE1FQFiSfU609F8ejGl5h9IAtbusnoS5LKc575N27YIjg80knIUnlqJ2uCEE3tFFqtGnmdK//M7hdsGfhdja5vkRxhzEULFLG70nZ5G6RSlnybGEwm6bURShU4ZNaeRTshulW+7p5i/pfVeSxKzF0BqR7qSbbiqmDlSIzOSXxwDGzeDw4XfuRUXYlyUll2StF8B46YxNSiDuGsz8L4o0ApDnilImauQ268BuxGj2B2ZS0hqELd43CXofcQwW0vkzaii5AgkmnEV1vhcu76TMVcEtLqAkH+N7L8vKdNhyp3WlgYzlKGnikxnIDw9fc5zX; csm-hit=tb:s-921679HQ2XQH75W6618E|1732702453187&t:1732702453899&adb:adblk_no; amp_389c1b=3961e650-93a6-45e8-9540-eca24b1e2495...1idmghb9q.1idmhqgp6.0.0.0';

        // Configurar streaming y encabezados
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        // Enviar HTML inicial
        //$html = file_get_contents(public_path('templates/product.html'));
        //die($html);
        // Asegurarse de enviar los datos al cliente
        flush();

        // Añadir un padding para evitar buffering
        echo str_repeat(" ", 1024);
        flush();
        $buffer = "";
        $globalBuffer = "";
        $config = [
        "title" => [
            "contains" => [
                'productTitle'
            ],
            "status" => "pending"
        ],
        "price" => [
            "contains" => [
                'corePrice_feature_div'
            ],
            "status" => "pending"
        ],
        "image" => [
            "contains" => [
                'imgTagWrapperId'
            ],
            "status" => "pending"
        ],
        "variant" => [
            "contains" => [
                'twisterDimKeys'
            ],
            "status" => "pending"
        ],
        "variant_color" => [
            "contains" => [
                'variation_color_name'
            ],
            "status" => "pending"
        ],
    ];

        //echo "1111<br>";
        flush();

        $proxyHost = env('OXYLABS_PROXY');
        $proxyPort = env('OXULABS_PORT');
        $proxyUser = env('OXYLABS_USER_US');
        $proxyPass = env('OXYLABS_PASS');

        // Configurar cURL
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

            CURLOPT_PROXY => $proxyHost, // Proxy host
            CURLOPT_PROXYPORT => $proxyPort, // Proxy port
            CURLOPT_PROXYUSERPWD => $proxyUser . ':' . $proxyPass, // Proxy authentication

            CURLOPT_BUFFERSIZE => 1024, // Reduce el tamaño del buffer de cURL
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) use ($productId, $try, $buffer, $config, $globalBuffer) {
                // Procesar cada fragmento del HTML



                $parsedChunk = AmazonProductParserV1::parse($chunk, $productId, $try, $buffer, $config, $globalBuffer);
                if ($parsedChunk) {
                    echo "<div class='result'>{$parsedChunk}</div>";
                    echo "<!-- chunk -->"; // Ayuda a forzar el rendering
                    echo str_repeat(" ", 1024); // Padding;
                    //retraso en milisegundos
                    //usleep(100000); // Delay de 0.5 segundos
                    flush(); // Enviar el chunk al navegador
                }
                return strlen($chunk);
            },
        ]);

        curl_exec($curl);

        if (curl_errno($curl)) {
            echo "<p>Error: " . curl_error($curl) . "</p>";
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

    private function getUserAgent(): string
    {
        return "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/89.0.4389.82 Safari/537.36";
    }
}
