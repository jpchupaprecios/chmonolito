<?php

namespace App\Http\Controllers;

use App\Helpers\SymfonyPanther;
use App\Models\ScrapingSession;
use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
use DOMDocument;
use DOMXPath;
use tidy;
use App\Parsers\Chapi\Amazon\Results\ChapiAmazonResultParser;
use App\Http\Controllers\BehaviorController;
class ResultController extends Controller
{
    protected const COOKIE_PATH = 'app/';

    private static  $userAgent;
    private function showLayout(){
        $layoutStart = file_get_contents(resource_path('views/layouts/layoutStart.blade.php'));
        $layoutStart = $this->showMarquee($layoutStart);
        $layoutStart = $this->showHeader($layoutStart);
        $layoutStart = $this->showCategories($layoutStart);
        $layoutStart = $this->showSearchWrapper($layoutStart);

        return $layoutStart;
    }



    private function showSearchWrapper($layoutStart){
        $searchWrapper = file_get_contents(resource_path('views/pages/result/index2.blade.php'));
        $searchBar = file_get_contents(resource_path('views/components/search.blade.php'));
        $layoutStart = str_replace('{{ //SEARCH}}', $searchBar, $layoutStart);
        $layoutStart = str_replace('{{ //CONTENT}}', $searchWrapper, $layoutStart);
        //CONTENT

        $pagination = file_get_contents(resource_path('views/pages/result/components/pagination.blade.php'));
        $filters = file_get_contents(resource_path('views/pages/result/components/filters.blade.php'));
        //remplazo {{ //FILTERS}}
        $layoutStart = str_replace('{{ //FILTERS}}', $filters, $layoutStart);
        //remplazo {{ //PAGINATION}}
        $layoutStart = str_replace('{{ //PAGINATION }}', $pagination, $layoutStart);
        return $layoutStart;
    }

    private function showMarquee($layoutStart){
        $marquee = file_get_contents(resource_path('views/components/marquee.blade.php'));
        $layoutStart = str_replace('{{ //MARQUEE }}', $marquee, $layoutStart);
        return $layoutStart;
    }

    private function showHeader($layoutStart){
        $header = file_get_contents(resource_path('views/components/header2.blade.php'));
        $auth = auth()->user();

        if($auth) {
            $lis = file_get_contents(resource_path('views/components/header2-loggedin.blade.php'));
        }else{
            $lis = file_get_contents(resource_path('views/components/header2-guest.blade.php'));
        }

        $minicart = file_get_contents(resource_path('views/components/minicart.blade.php'));

        $lis = str_replace('{{ //MINICART }}', $minicart, $lis);

        $header = str_replace('{{ //LIS }}', $lis, $header);
        $layoutStart = str_replace('{{ //HEADER }}', $header, $layoutStart);
        return $layoutStart;
    }

    private function showCategories($layoutStart){
        $categories = file_get_contents(resource_path('views/components/categories.blade.php'));
        $layoutStart = str_replace('{{ //CATEGORIES}}', $categories, $layoutStart);
        return $layoutStart;
    }

    public function index(Request $request, $csi)
    {
        // Configurar streaming y encabezados
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        echo $this->showLayout();

        $query = $request->input('q');
        $store = $request->input('s');
        $page = $request->input('page', 1);

        $url = "https://www.amazon.com/s?k=" . urlencode($query) . "&language=es_US&page={$page}";
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = '';
        $scrapingSession = ScrapingSession::where("client_session_id", $csi)->first();

        if ($scrapingSession) {
            $cookie = ($scrapingSession->amazon_cookie) ? $scrapingSession->amazon_cookie : "";
            self::$userAgent = ($scrapingSession->user_agent) ? $scrapingSession->user_agent : "";
        }

        if (!$scrapingSession) {
            $scrapingSession = new ScrapingSession();
            $cookies = SymfonyPanther::getCookies($url);
            if ($cookies) {
                $userAgent = $cookies["user-agent"];
                $cookies = $cookies["cookies"];

                $scrapingSession->client_session_id = $csi;
                $cookieStr = "";
                foreach ($cookies as $cookie) {
                    $cookieStr .= $cookie . ";";
                }
                $scrapingSession->amazon_cookie = $cookieStr;
                $scrapingSession->user_agent = $userAgent;
                $scrapingSession->save();

                if ($scrapingSession) {
                    $cookie = ($scrapingSession->amazon_cookie) ? $scrapingSession->amazon_cookie : "";
                    self::$userAgent = ($scrapingSession->user_agent) ? $scrapingSession->user_agent : "";
                }
            }
        }

        // Enviar HTML inicial
        flush();
        $usedAsins = [];
        $counter = 0;
        $bufferLimited = "";
        $global = "";
        $countParsedElements = 0;
        $countParsedElementsFail = 0;

        // Añadir un padding para evitar buffering
        echo str_repeat(" ", 1024);
        flush();

        $proxies = [
            [
                'host' => 'dc.oxylabs.io',
                'port' => 8000,
                'user' => 'user-chupaprecios_lDWEa-country-US',
                'pass' => '+Aq1w2e3r4t5'
            ],
            /*[
                'host' => 'us-pr.oxylabs.io',
                'port' => 10000,
                'user' => 'customer-chupaprecios_COPc9_K2KrH',
                'pass' => '+Aq1w2e3r4t5'
            ],*/
            [
                'host' => 'pr.oxylabs.io',
                'port' => 7777,
                'user' => 'customer-jotapey3_qcf4a-cc-us',
                'pass' => '+Aq1w2e3r4t5'
            ],
            /*[
                'host' => 'pr.oxylabs.io',
                'port' => 7777,
                'user' => 'customer-jotapey2_Kr8Ew-cc-us',
                'pass' => '2H5zdvxVQff'
            ]*/
        ];

        $multiCurl = curl_multi_init();
        $handles = [];
        $winnerHandle = null;
        $firstValidResponse = false;

        foreach ($proxies as $proxy) {
            $curl = curl_init($url);
            curl_setopt_array($curl, [
                CURLOPT_HTTPHEADER => self::getHeaders($cookie),
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_RETURNTRANSFER => false, // Deshabilita retorno automático
                CURLOPT_COOKIEFILE => $cookiePath,
                CURLOPT_COOKIEJAR => $cookiePath,
                CURLOPT_USERAGENT => self::getUserAgent(),
                CURLOPT_TIMEOUT => 30,
                CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
                CURLOPT_CONNECTTIMEOUT => 5,
                CURLOPT_ENCODING => '',
                CURLOPT_PROXY => $proxy['host'],
                CURLOPT_PROXYPORT => $proxy['port'],
                CURLOPT_PROXYUSERPWD => $proxy['user'] . ':' . $proxy['pass'],
                CURLOPT_BUFFERSIZE => 256,
                CURLOPT_WRITEFUNCTION => function ($ch, $chunk) use (&$usedAsins, &$counter, &$bufferLimited, &$global, &$countParsedElements, &$countParsedElementsFail, &$firstValidResponse, &$winnerHandle, &$csi) {
                    // Si ya hay respuesta válida
                    if ($firstValidResponse) {
                        // Abortamos cualquier handle que no sea el ganador
                        if ($ch !== $winnerHandle) {
                            return 0;
                        }
                    }

                    $global .= $chunk;
                    $parsedProducts = AmazonSearchParser::parse($chunk, $usedAsins, $counter, $bufferLimited, $countParsedElements, $countParsedElementsFail);

                    if ($parsedProducts && is_countable($parsedProducts) && count($parsedProducts) > 0) {
                        if (!$winnerHandle) {
                            $winnerHandle = $ch;
                        }
                        $firstValidResponse = true;

                        // Acumulamos el HTML de los productos
                        $productHtml = '';
                        foreach ($parsedProducts as $parsedProduct) {
                            $parsedProduct["csi"] = $csi;
                            $productHtml .= view('pages.result.components.product', [
                                'productData' => $parsedProduct
                            ])->render();
                        }

                        // Enviamos el HTML acumulado
                        echo "<script>document.querySelector('#product-container')
        .insertAdjacentHTML('beforeend', `" . addslashes($productHtml) . "` );</script>";
                        flush();

                        // Agregamos un pequeño separador que ayude al navegador a "pintar"
                        echo "<!-- chunk -->";
                        echo str_repeat(" ", 1024);
                        flush();
                    }

                    // Devolvemos la cantidad de bytes procesados
                    return strlen($chunk);
                }
            ]);

            curl_multi_add_handle($multiCurl, $curl);
            $handles[] = $curl;
        }

        do {
            $status = curl_multi_exec($multiCurl, $active);
            // Opcionalmente un pequeño timeout en select para no bloquear mucho
            curl_multi_select($multiCurl, 0.2);

            // Si ya hay un handle ganador
            if ($firstValidResponse && $winnerHandle) {
                // Quitar del multiCurl todos los demás
                foreach ($handles as $curl) {
                    if ($curl !== $winnerHandle) {
                        curl_multi_remove_handle($multiCurl, $curl);
                        curl_close($curl);
                    }
                }
                // Dejamos en $handles solo el ganador
                $handles = [$winnerHandle];
                // No hacemos break, para terminar de leer el HTML completo
            }
        } while ($active && $status == CURLM_OK);

        // Cerrar el handle final
        foreach ($handles as $curl) {
            curl_multi_remove_handle($multiCurl, $curl);
            curl_close($curl);
        }

        curl_multi_close($multiCurl);

        $cleanHtml = self::repairHtml($global);
        $this->resultParser = new ChapiAmazonResultParser();
        $resultParse = $this->resultParser->parse($cleanHtml, "amazon", $query, false);

        if ($resultParse) {
            echo "<script>resultsC = JSON.parse('" . addslashes(json_encode($resultParse)) . "');</script>";
        }

        // Finalizar la página HTML
        $endLayout = file_get_contents(resource_path('views/layouts/layoutEnd.blade.php'));
        $footer = file_get_contents(resource_path('views/components/footer.blade.php'));
        $endLayout = str_replace('{{ //QUERY }}', 'q=' . $query, $endLayout);
        $endLayout = str_replace('{{ //FOOTER}}', $footer, $endLayout);
        echo $endLayout;
        flush(); // Asegurarse de enviar el contenido final
    }

    private static function repairHtml(string $html): string
    {
        // Usa tidy si está disponible
        if (extension_loaded('tidy')) {
            $config = [
                'indent' => true,
                'output-xhtml' => true,
                'wrap' => 200,
                'input-encoding' => 'utf8',
                'output-encoding' => 'utf8',
                'char-encoding' => 'utf8',
            ];
            $tidy = new tidy();
            $cleanHtml = $tidy->repairString($html, $config, 'utf8');
            return $cleanHtml;
        }

        // Fallback: Agregar etiquetas básicas si tidy no está disponible
        if (stripos($html, '<html') === false) {
            $html = "<html><body>{$html}</body></html>";
        }

        return $html;
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
        if(self::$userAgent){
            return self::$userAgent;
        }
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
