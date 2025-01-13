<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
class ResultController extends Controller
{
    protected const COOKIE_PATH = 'app/';


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

    public function index(Request $request)
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

        //return view('pages.result.index');

        $url = "https://www.amazon.com/s?k=" . urlencode($query) . "&language=es_US&page={$page}";
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'skin=noskin; session-id=133-0374827-0627531; session-id-time=2082787201l; i18n-prefs=USD; ubid-main=132-3903942-5106812; session-token=QIlYNwirLg5xMnGixDgE37MZB5bKMZDTq2aINLM163f0CDiDnHrlWWaiZJEa7/iSbMqHY8enYF3pXhTBv26fzqliS3TTLAB61ZkxCdtdqYEZ31+dV4AB0Mio+UKGjKqjTmf1TDAR3dX1BTMW9qb6uhhqJMLQBEEenBQC74RpCIKbl5Oi081EkNiREVOkzOjlglnexlxhBNbTvRhxf7Qw2wdwJloOe+b7cG0aBFCbNQxlcJ5tOFvfd+pMKXeqPclmMHeIQGMDqc7QONDqI3YvXV6f/s5nkXE4eVLDHklQwTfnZSFQdseDtM6dkkXp+erdTTMfIHMiaBjoaWGq3SAYBkDB6+F5iFgV; csm-hit=tb:s-AZDX3NKC7W0Z9XPN7W71|1735126737193&t:1735126737289&adb:adblk_no; amp_389c1b=3961e650-93a6-45e8-9540-eca24b1e2495...1ifupplbe.1ifuppoik.0.0.0';

        // Enviar HTML inicial

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
                // Supongamos que parse() retorna un array de productos
                $parsedProducts = AmazonSearchParser::parse($chunk);

                if ($parsedProducts && is_countable($parsedProducts) && count($parsedProducts) > 0) {
                    // Iteras sobre cada producto y renderizas la vista product.blade.php

                        $productHtml = view('pages.result.components.product', [
                            'productData' => $parsedProducts
                        ])->render();

                        echo "<script>document.querySelector('#product-container')
    .insertAdjacentHTML('beforeend', `" . addslashes($productHtml) . "` );</script>";
                        flush();



                    // Agregas un pequeño separador que ayude al navegador a "pintar"
                    echo "<!-- chunk -->";
                    echo str_repeat(" ", 1024);
                    flush();
                }

                // IMPORTANTE: devolver el número de bytes procesados,
                // para que cURL sepa que todo se manejó bien.
                return strlen($chunk);
            }

        ]);
        /**/

        curl_exec($curl);

        if (curl_errno($curl)) {
            echo "<p>Error: " . curl_error($curl) . "</p>";
        }

        curl_close($curl);

        // Finalizar la página HTML
        $endLayout = file_get_contents(resource_path('views/layouts/layoutEnd.blade.php'));
        $footer = file_get_contents(resource_path('views/components/footer.blade.php'));
        $endLayout = str_replace('{{ //PRODUCT_ID }}', 'q='.$query, $endLayout);
        $endLayout = str_replace('{{ //FOOTER}}', $footer, $endLayout);
        echo $endLayout;
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
