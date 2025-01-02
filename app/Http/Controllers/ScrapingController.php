<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
class ScrapingController extends Controller
{
    protected const COOKIE_PATH = 'app/';

    public function showFormAndabFlush()
    {
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        for ($i = 0; $i < 20; $i++) {
            echo "<p>Chunk $i</p>";
            echo str_repeat(" ", 1024); // Padding
            flush();
            usleep(500000); // Delay
        }
    }

    public function performScraping(Request $request)
    {
        $query = $request->input('query');
        $page = $request->input('page', 1);
        $url = "https://www.amazon.com/s?k=" . urlencode($query) . "&language=es_US&page={$page}";
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'skin=noskin; session-id=133-0374827-0627531; session-id-time=2082787201l; i18n-prefs=USD; ubid-main=132-3903942-5106812; session-token=QIlYNwirLg5xMnGixDgE37MZB5bKMZDTq2aINLM163f0CDiDnHrlWWaiZJEa7/iSbMqHY8enYF3pXhTBv26fzqliS3TTLAB61ZkxCdtdqYEZ31+dV4AB0Mio+UKGjKqjTmf1TDAR3dX1BTMW9qb6uhhqJMLQBEEenBQC74RpCIKbl5Oi081EkNiREVOkzOjlglnexlxhBNbTvRhxf7Qw2wdwJloOe+b7cG0aBFCbNQxlcJ5tOFvfd+pMKXeqPclmMHeIQGMDqc7QONDqI3YvXV6f/s5nkXE4eVLDHklQwTfnZSFQdseDtM6dkkXp+erdTTMfIHMiaBjoaWGq3SAYBkDB6+F5iFgV; csm-hit=tb:s-AZDX3NKC7W0Z9XPN7W71|1735126737193&t:1735126737289&adb:adblk_no; amp_389c1b=3961e650-93a6-45e8-9540-eca24b1e2495...1ifupplbe.1ifuppoik.0.0.0';

        // Configurar streaming y encabezados
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        // Enviar HTML inicial
        echo file_get_contents(storage_path('app/template/result/before.html'));
        echo '<main id="main" class="bg-white">';


        // Asegurarse de enviar los datos al cliente
        flush();

        // Añadir un padding para evitar buffering
        echo str_repeat(" ", 1024);
        flush();
        $proxyHost = env('OXYLABS_PROXY');
        $proxyPort = env('OXULABS_PORT');
        $proxyUser = env('OXYLABS_USER_US');
        $proxyPass = env('OXYLABS_PASS');
        // Configurar cURL
        $curl = curl_init($url);

        echo file_get_contents(storage_path('app/template/result/InitScript.html'));

        /**/
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

            /*
            CURLOPT_PROXY => $proxyHost, // Proxy host
            CURLOPT_PROXYPORT => $proxyPort, // Proxy port
            CURLOPT_PROXYUSERPWD => $proxyUser . ':' . $proxyPass, // Proxy authentication
            */

            CURLOPT_BUFFERSIZE => 1024, // Reduce el tamaño del buffer de cURL
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) {
                // Procesar cada fragmento del HTML
                $parsedChunk = AmazonSearchParser::parse($chunk);
                if ($parsedChunk) {
                    echo $parsedChunk;
                    echo "<!-- chunk -->"; // Ayuda a forzar el rendering
                    echo str_repeat(" ", 1024); // Padding;
                    //retraso en milisegundos
                    //usleep(100000); // Delay de 0.5 segundos
                    flush(); // Enviar el chunk al navegador
                }
                return strlen($chunk);
            },
        ]);
        /**/

        curl_exec($curl);

        if (curl_errno($curl)) {
            echo "<p>Error: " . curl_error($curl) . "</p>";
        }

        curl_close($curl);

        // Finalizar la página HTML
        echo file_get_contents(storage_path('app/template/result/after.html'));
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
