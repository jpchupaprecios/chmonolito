<?php

namespace App\Http\Controllers;

use App\Helpers\AmazonProductParser;
use App\Helpers\SymfonyPanther;
use App\Models\ScrapingSession;
use App\Parsers\Chapi\Amazon\Product\Variants\ChapiAmazonVariantsParser;
use App\Services\CookieService;
use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
use DOMDocument;
use DOMXPath;
use App\Models\Product;
use App\Parsers\Chapi\Amazon\Complete\Product\ChapiAmazonProductDetailParser;
use App\Helpers\Price;
class ProductController extends Controller
{
    private static $userAgent;
    protected const COOKIE_PATH = 'app/';

    private function showLayout($id){
        $layoutStart = file_get_contents(resource_path('views/layouts/layoutStart.blade.php'));
        $layoutStart = $this->showMarquee($layoutStart);
        $layoutStart = $this->showHeader($layoutStart);
        $layoutStart = $this->showCategories($layoutStart);
        $layoutStart = $this->showSearchWrapper($layoutStart, $id);
        $layoutStart = $this->showFav($layoutStart);
        $layoutStart = $this->showBreadcrumb($layoutStart);
        $layoutStart = $this->showQuantityControls($layoutStart);

        return $layoutStart;
    }

    private function showSearchWrapper($layoutStart, $id){
        $searchWrapper = file_get_contents(resource_path('views/pages/details/index2.blade.php'));
        $searchBar = file_get_contents(resource_path('views/components/search.blade.php'));
        $layoutStart = str_replace('{{ //SEARCH}}', $searchBar, $layoutStart);
        $layoutStart = str_replace('{{ //CONTENT}}', $searchWrapper, $layoutStart);
        $layoutStart = str_replace('{{ //selectedVariantAsin}}', $id, $layoutStart);
        //CONTENT

        return $layoutStart;
    }

    private function showMarquee($layoutStart){
        $marquee = file_get_contents(resource_path('views/components/marquee.blade.php'));
        $layoutStart = str_replace('{{ //MARQUEE }}', $marquee, $layoutStart);
        return $layoutStart;
    }

    private function showFav($layoutStart){
        $fav = file_get_contents(resource_path('views/pages/details/components/fav.blade.php'));
        $layoutStart = str_replace('<!-- fav -->', $fav, $layoutStart);
        return $layoutStart;
    }

    private function showBreadcrumb($layoutStart){
        $breadcrumb = file_get_contents(resource_path('views/pages/details/components/breadcrumb.blade.php'));
        $layoutStart = str_replace('<!-- breadcrumb -->', $breadcrumb, $layoutStart);
        return $layoutStart;
    }

    private function showQuantityControls($layoutStart){
        $fav = file_get_contents(resource_path('views/pages/details/components/quantity-controls.blade.php'));
        $layoutStart = str_replace('<!-- quantity-controls -->', $fav, $layoutStart);
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

    private function showSelectVariant($layoutStart, $variant){
        $variants = $variant["options"];
        $title = $variant["title"];
        $selectDiv = file_get_contents(resource_path('views/pages/details/components/selects2.blade.php'));
        $select = '<option value="">Seleccionar</option>';
        $li = '';

        if(!$title){
            $title = $variant["name"];
            if($title){
                $title = ucwords(str_replace('_', ' ', $title));

                if($title == "Size Name"){
                    $title = __("Tamaño");
                }elseif($title == "Color Name"){
                    $title = __("Color");
                }elseif($title == "Service Provider"){
                    $title = __("Proveedor");
                }

                $title = str_replace('Name', '', $title);
            }
        }

        $selectDiv = str_replace('<!-- select_name -->', $title, $selectDiv);

        foreach($variants as $variant){
            $selected = ($variant["selected"]) ? "selected" : "";
            $available = (!$variant["available"]) ? "disabled" : "";
            $select .=
                '<option '. $selected .' '. $available .' value="'.$variant["sku"].'">'.$variant["text"].'</option>';
        }
        $selectDiv = str_replace('<!-- options -->', $select, $selectDiv);



        foreach($variants as $variant){
            $li .=
                '<li class="px-3 py-2 hover:bg-gray-100 cursor-pointer" data-size="'.$variant["sku"].'">'.$variant["text"].'</li>';
        }
        $selectDiv = str_replace('<!-- lis -->', $li, $selectDiv);


        return $selectDiv;
    }

    private function showColorVariant($layoutStart, $variant){
        $variants = $variant["options"];
        $title = $variant["title"];

        $colorsDiv = file_get_contents(resource_path('views/pages/details/components/color-options2.blade.php'));
        $colors = "";
        $variantSelected = "";
        foreach($variants as $variant){

            $selected = ($variant["selected"]) ? ' ring-2 ring-offset-2 ring-blue-500' : '';
            $classSelected = "";
            if($selected){
                $a = 1;
                //color-button w-16 border-2 focus:outline-none focus:ring-2 focus:ring-offset-2 border-gray-300 ring-2 ring-offset-2 ring-blue-500
                //color-button w-16 border-2 focus:outline-none focus:ring-2 focus:ring-offset-2 border-gray-300
                $classSelected = "selected-variant";
                $variantSelected = $variant["sku"];
            }
            $colors .=
            "<img " .
                'data-sku="'.$variant["sku"].'"'.
                'class="'.$classSelected.' color-button w-16 border-2 focus:outline-none focus:ring-2 focus:ring-offset-2 border-gray-300 '.$selected.'"'.
                'src="'.$variant["img"].'" />';
        }

        $colorsDiv = str_replace('<!-- color_variants -->', $colors, $colorsDiv);
        $colorsDiv = str_replace('<!-- color_variant_value -->', $variantSelected, $colorsDiv);

        return $colorsDiv;
    }

    public function index(Request $request, $id, $vendor, $csi = null)
    {
        $product = false;
        //$product = Product::where("product_id", $id)->where("vendor", $vendor)->first();


        // Configura las cabeceras para streaming
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        // Imprimimos el layout base
        echo $this->showLayout($id);
        flush();

        // Añadimos un pequeño relleno para forzar el envío de datos
        echo str_repeat(" ", 1024);
        flush();

        // Ajusta la URL y cookie
        $url = 'https://www.amazon.com/dp/' . $id;
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        //$cookie = 'session-id=145-2848617-2390738; i18n-prefs=USD; ...'; // tu cookie
        //$scrapingSession = ScrapingSession::where("client_session_id", $client_session_id)->first();
        $cookie = '';
        $scrapingSession = null;

        if($csi){
            $scrapingSession = ScrapingSession::where("client_session_id", $csi)->first();

            if($scrapingSession){
                $cookie = ($scrapingSession->amazon_cookie) ? $scrapingSession->amazon_cookie : "";
                self::$userAgent = ($scrapingSession->user_agent) ? $scrapingSession->user_agent : "";
            }

            if(!$scrapingSession) {
                $scrapingSession = new ScrapingSession();
                $cookies = SymfonyPanther::getCookies($url);
                if($cookies){
                    $userAgent = $cookies["user-agent"];
                    $cookies = $cookies["cookies"];


                    $scrapingSession->client_session_id = $csi;
                    $cookieStr = "";
                    foreach($cookies as $cookie){
                        $cookieStr .= $cookie . ";";
                    }
                    $scrapingSession->amazon_cookie = $cookieStr;
                    $scrapingSession->user_agent = $userAgent;
                    $scrapingSession->save();

                    if($scrapingSession){
                        $cookie = ($scrapingSession->amazon_cookie) ? $scrapingSession->amazon_cookie : "";
                        self::$userAgent = ($scrapingSession->user_agent) ? $scrapingSession->user_agent : "";
                    }
                }
            }
        }

        // Variables que usaremos para parsear
        $datas = [
            "title" => ["status" => "pending", "content" => ""],
            "price" => ["status" => "pending", "content" => ""],
            "image" => ["status" => "pending", "content" => ""],
            "rating" => ["status" => "pending", "content" => ""],
            "thumbs" => ["status" => "pending", "content" => ""],
            "variant" => ["status" => "pending", "content" => ""],
            "variant_color" => ["status" => "pending", "content" => ""],
        ];

        $global         = "";
        $formVariants   = "";
        $alreadyVariants= false;
        $variantsForm   = false;
        $variantsDiv    = false;
        $thumbsChunks   = "";
        $imagesThumb    = null;

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

        $multiCurl          = curl_multi_init();
        $handles            = [];
        $winnerHandle       = null;
        $firstValidResponse = false;
        $buffer             = '';  // si tu parser necesita un buffer global

        if(!$product){
            //$product = new Product();
            // Construimos un handle por cada proxy
            foreach ($proxies as $proxy) {
                $curl = curl_init($url);
                curl_setopt_array($curl, [
                    CURLOPT_HTTPHEADER     => self::getHeaders($cookie),
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_RETURNTRANSFER => false, // Deshabilita retorno automático
                    CURLOPT_COOKIEFILE     => $cookiePath,
                    CURLOPT_COOKIEJAR      => $cookiePath,
                    CURLOPT_USERAGENT      => self::getUserAgent(),
                    CURLOPT_TIMEOUT        => 30,
                    CURLOPT_CONNECTTIMEOUT => 5,
                    CURLOPT_ENCODING       => '',
                    CURLOPT_PROXY          => $proxy['host'],
                    CURLOPT_PROXYPORT      => $proxy['port'],
                    CURLOPT_PROXYUSERPWD   => $proxy['user'] . ':' . $proxy['pass'],
                    CURLOPT_BUFFERSIZE     => 1024,
                    CURLOPT_WRITEFUNCTION  => function ($ch, $chunk) use (
                        &$buffer,
                        &$datas,
                        $id,
                        &$global,
                        &$formVariants,
                        &$alreadyVariants,
                        &$variantsForm,
                        &$variantsDiv,
                        &$thumbsChunks,
                        &$imagesThumb,
                        &$firstValidResponse,
                        &$winnerHandle,
                        &$product
                    ) {
                        // Si ya hay respuesta válida
                        if ($firstValidResponse) {
                            // Abortamos cualquier handle que no sea el ganador
                            if ($ch !== $winnerHandle) {
                                return 0;
                            }
                        }

                        // Buscar thumbs
                        if (!$imagesThumb && strpos($chunk, '[{"hiRes') !== false) {
                            $thumbsChunks .= $chunk;
                            $imagesThumb = self::getImages($thumbsChunks);

                            if ($imagesThumb) {
                                // Asignamos el handle ganador si todavía no lo hemos hecho
                                if (!$winnerHandle) {
                                    $winnerHandle = $ch;
                                }
                                $firstValidResponse = true;

                                echo "<script>pData.thumbs = JSON.parse('" . json_encode($imagesThumb) . "');</script>";
                                $html = '<div class="flex space-x-2 thumbnails">';
                                foreach ($imagesThumb as $image) {
                                    $html .= '
                                    <img
                                        alt=""
                                        loading="lazy"
                                        decoding="async"
                                        data-nimg="fill"
                                        class="rounded-md thumb-img"
                                        src="' . $image . '"
                                        style="position: absolute; height: 100%; width: 100%; inset: 0px; object-fit: cover; color: transparent;"
                                    >
                                ';
                                }
                                $html .= '</div>';
                                $escapedHtml = json_encode($html);
                                echo "<script>
                                var content   = $escapedHtml;
                                var container = document.querySelector('#thumbnails-wrapper');
                                if (container) {
                                    container.insertAdjacentHTML('beforeend', content);
                                }
                            </script>";
                            }
                        }

                        // Buscar variantes
                        if (!$alreadyVariants) {
                            if ($formVariants) {
                                $formVariants .= $chunk;
                            }
                            if (strpos($chunk, 'twister-plus-inline-twister') !== false) {
                                $formVariants .= $chunk;
                                $variantsDiv = true;
                            }
                            if (strpos($chunk, 'form id="twiste') !== false) {
                                $formVariants .= $chunk;
                                $variantsForm = true;
                            }
                            if ($variantsDiv) {
                                if ($formVariants && strpos($chunk, 'dp-cif aok-hidden') !== false) {
                                    $formVariants .= $chunk;
                                    $this->parseVariants($id, $formVariants);
                                    $alreadyVariants = true;
                                    // Asignamos handle ganador si no lo teníamos
                                    if (!$winnerHandle) {
                                        $winnerHandle = $ch;
                                    }
                                    $firstValidResponse = true;
                                    $formVariants       = "";
                                }
                            }
                            if ($variantsForm) {
                                if ($formVariants && strpos($chunk, '</form') !== false) {
                                    $formVariants .= $chunk;
                                    $this->parseVariants($id, $formVariants);
                                    $alreadyVariants = true;
                                    if (!$winnerHandle) {
                                        $winnerHandle = $ch;
                                    }
                                    $firstValidResponse = true;
                                    $formVariants       = "";
                                }
                            }
                        }

                        // Acumulamos en $global
                        $global .= $chunk;

                        // Parseamos chunk a chunk (si tu parser lo requiere)
                        $parsedProducts = AmazonProductParser::processHtmlChunks($chunk, $buffer, $datas, $id);

                        if ($parsedProducts && is_countable($parsedProducts) && count($parsedProducts) > 0) {
                            // Asignamos el ganador si todavía no está
                            if (!$winnerHandle) {
                                $winnerHandle = $ch;
                            }
                            $firstValidResponse = true;

                            // Vemos qué tipo de dato se ha extraído
                            if (isset($parsedProducts['price'])) {
                                $price = $parsedProducts['price'];
                                $price = Price::cotizarDolar($price);
                                ////$product->price = $price;
                                echo "<script>
                                pData.price = " . addslashes($price) . ";
                                document.querySelector('.price-shimmer').style.display = 'none';
                                document.querySelector('.product-data-price').textContent = '$ " . addslashes($price) . " MXN';
                            </script>";
                            }
                            if (isset($parsedProducts['title'])) {
                                $title = $parsedProducts['title'];
                                //$product->title = $title;
                                echo "<script>
                                pData.title = '" . addslashes($title) . "';
                                document.querySelector('.title-shimmer-wrapper').style.display = 'none';
                                document.querySelector('.product-data-title').textContent = '" . addslashes($title) . "';
                            </script>";
                            }
                            if (isset($parsedProducts['image'])) {

                                $imageUrl = $parsedProducts['image'];
                                //$product->image = $imageUrl;
                                echo "<script>
                                pData.image = '" . addslashes($imageUrl) . "';
                                const imgEl        = document.querySelector('.product-data-image');
                                const imgElShimmer = document.querySelector('.image-placeholder');
                                imgElShimmer.style.display = 'none';
                                imgEl.style.display = 'block';
                                imgEl.src = '" . addslashes($imageUrl) . "';
                                imgEl.alt = 'Imagen del producto';
                            </script>";
                            }
                            if (isset($parsedProducts['rating'])) {
                                $rating      = floatval($parsedProducts['rating']);
                                //$product->rating = $rating;
                                $fullStars   = floor($rating);
                                $decimalPart = $rating - $fullStars;
                                if ($decimalPart > 0) {
                                    if ($decimalPart >= 0.6) {
                                        $fullStars++;
                                        $halfStar = 0;
                                    } else {
                                        $halfStar = 1;
                                    }
                                } else {
                                    $halfStar = 0;
                                }
                                if ($fullStars > 5) {
                                    $fullStars = 5;
                                    $halfStar  = 0;
                                }
                                $emptyStars = 5 - ($fullStars + $halfStar);

                                $starsHtml = "";
                                for ($i = 0; $i < $fullStars; $i++) {
                                    $starsHtml .= '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1
                                    1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034
                                    a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                    1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838
                                    -.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98
                                    8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951
                                    -.69l1.07-3.292z"></path></svg>';
                                }
                                if ($halfStar) {
                                    $starsHtml .= '<svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <defs>
                                        <linearGradient id="half-star">
                                            <stop offset="50%" stop-color="#facc15"/>
                                            <stop offset="50%" stop-color="#d1d5db"/>
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#half-star)" d="M9.049 2.927c.3-.921
                                    1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                                    0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364
                                    1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8
                                    -2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838
                                    -.197-1.539-1.118l1.07-3.292a1 1 0 00-.364
                                    -1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461
                                    a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>';
                                }
                                for ($i = 0; $i < $emptyStars; $i++) {
                                    $starsHtml .= '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902
                                    0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371
                                    1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07
                                    3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1
                                    1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197
                                    -1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98
                                    8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0
                                    00.951-.69l1.07-3.292z"></path></svg>';
                                }
                                $starsHtml .= '<span class="ml-2 text-gray-600 product-data-rating">' . addslashes($rating) . '</span>';

                                $escapedStarsHtml = json_encode($starsHtml);
                                echo "<script>
                                pData.rating = " . addslashes($rating) . ";
                                document.querySelector('.rating-stars-wrapper').style.display = 'flex';
                                document.querySelector('.color-shimmer-options').style.display = 'none';
                                document.querySelector('.product-data-rating').textContent = '" . addslashes($rating) . "';
                                document.querySelector('.rating-stars-wrapper').innerHTML = $escapedStarsHtml;
                            </script>";
                            }

                            // Enviamos pequeños chunks para forzar el flush
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

            // Ejecutamos el bucle principal de multi cURL
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

            //$product->product_id = $id;
            //$product->vendor = $vendor;
            //$product->save();

            if ($global) {

            // Parseo final (p. ej. si quieres extraer más cosas con tu parser “completo”)
            $chapiAmazonProductDetailParser = new ChapiAmazonProductDetailParser($cookie);
            $data = $chapiAmazonProductDetailParser->parse(["result" => $global], "amazon", $id, $cookie);

            //$product->save();

            $dom = new DOMDocument();
            @$dom->loadHTML($global);
            $this->xpath = new DOMXPath($dom);
            $variantsParser = new ChapiAmazonVariantsParser($this->xpath);
            $variants = $variantsParser->parse($id, (int)$id, $dom);
            if ($data && $variants) {
                $data->variants = $variants;
            }
            if ($data) {
                echo "<script>product = JSON.parse('" . addslashes(json_encode($data)) . "');</script>";
                echo "<script>
                if(product){
                    combinations = product.combination_separator;
                    combinationSeparator = product.combinations;
                    parsepDataC(pData, product);
                }
                </script>";

            }
        }

        }else{
            /*
                $price = $product->price;
                echo "<script>
                                pData.price = '" . addslashes($price) . "';
                                document.querySelector('.price-shimmer').style.display = 'none';
                                document.querySelector('.product-data-price').textContent = '$ " . addslashes($price) . " MXN';
                            </script>";
                $title = //$product->title;
                echo "<script>
                                pData.title = '" . addslashes($title) . "';
                                document.querySelector('.title-shimmer-wrapper').style.display = 'none';
                                document.querySelector('.product-data-title').textContent = '" . addslashes($title) . "';
                            </script>";
                $imageUrl = //$product->image;
                echo "<script>
                                pData.image = '" . addslashes($imageUrl) . "';
                                const imgEl        = document.querySelector('.product-data-image');
                                const imgElShimmer = document.querySelector('.image-placeholder');
                                imgElShimmer.style.display = 'none';
                                imgEl.style.display = 'block';
                                imgEl.src = '" . addslashes($imageUrl) . "';
                                imgEl.alt = 'Imagen del producto';
                            </script>";
                $rating = //$product->rating;
                $fullStars   = floor($rating);
                $decimalPart = $rating - $fullStars;
                if ($decimalPart > 0) {
                    if ($decimalPart >= 0.6) {
                        $fullStars++;
                        $halfStar = 0;
                    } else {
                        $halfStar = 1;
                    }
                } else {
                    $halfStar = 0;
                }
                if ($fullStars > 5) {
                    $fullStars = 5;
                    $halfStar  = 0;
                }
                $emptyStars = 5 - ($fullStars + $halfStar);

                $starsHtml = "";
                for ($i = 0; $i < $fullStars; $i++) {
                    $starsHtml .= '<svg class="w-5 h-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1
                                    1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034
                                    a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54
                                    1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838
                                    -.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98
                                    8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951
                                    -.69l1.07-3.292z"></path></svg>';
                }
                if ($halfStar) {
                    $starsHtml .= '<svg class="w-5 h-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <defs>
                                        <linearGradient id="half-star">
                                            <stop offset="50%" stop-color="#facc15"/>
                                            <stop offset="50%" stop-color="#d1d5db"/>
                                        </linearGradient>
                                    </defs>
                                    <path fill="url(#half-star)" d="M9.049 2.927c.3-.921
                                    1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969
                                    0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364
                                    1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8
                                    -2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838
                                    -.197-1.539-1.118l1.07-3.292a1 1 0 00-.364
                                    -1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461
                                    a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>';
                }
                for ($i = 0; $i < $emptyStars; $i++) {
                    $starsHtml .= '<svg class="w-5 h-5 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902
                                    0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371
                                    1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07
                                    3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1
                                    1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197
                                    -1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98
                                    8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0
                                    00.951-.69l1.07-3.292z"></path></svg>';
                }
                $starsHtml .= '<span class="ml-2 text-gray-600 product-data-rating">' . addslashes($rating) . '</span>';

                $escapedStarsHtml = json_encode($starsHtml);
                echo "<script>
                                pData.rating = '" . addslashes($rating) . "';
                                document.querySelector('.rating-stars-wrapper').style.display = 'flex';
                                document.querySelector('.color-shimmer-options').style.display = 'none';
                                document.querySelector('.product-data-rating').textContent = '" . addslashes($rating) . "';
                                document.querySelector('.rating-stars-wrapper').innerHTML = $escapedStarsHtml;
                            </script>";
            */
        }

        // Cerramos el HTML, footer y flush final
        $endLayout = file_get_contents(resource_path('views/layouts/layoutEnd.blade.php'));
        $footer    = file_get_contents(resource_path('views/components/footer.blade.php'));
        $endLayout = str_replace('{{ //QUERY }}', 'pid=' . $id, $endLayout);
        $endLayout = str_replace('{{ //FOOTER}}', $footer, $endLayout);

        echo $endLayout;
        flush();
    }

    private static function getImages($thumbsChunks): array
    {
        $dom = new DOMDocument();
        $dom->loadHTML($thumbsChunks);
        $xpath = new DOMXPath($dom);

        $images = [];
        $tes1 = false;

        $imageBlock = $xpath->document->textContent;
        if ($imageBlock) {
            $tes1 = trim($xpath->document->textContent);
        }

        if (!$tes1) {
            return $images;
        }

        $bb1 = null;
        $aa1 = substr($tes1, 0, strrpos($tes1, 'colorToAsin') - 4);
        $p = strpos($aa1, '[{"hiRes');
        if ($p) {
            $bb1 = substr($aa1, $p);
        }

        if ($bb1) {
            $bb1 = str_replace("\n", ' ', $bb1);
            $bb1 = substr($bb1, 0, strpos($bb1, '}]},'));
            $bb1 .= '}]';
            $s = trim($bb1);
            $images = json_decode($s, true);
        } else {
            $imgCanvas = $xpath->query('//*[@id="img-canvas"]')->item(0);
            if ($imgCanvas) {
                $dom = new DOMDocument();
                @$dom->loadHTML($imgCanvas->ownerDocument->saveHTML($imgCanvas));
                $imageElements = $dom->getElementsByTagName('img');
                $tmpImages = [];
                foreach ($imageElements as $image) {
                    if (strpos($image->getAttribute('src'), 'jpg') !== false) {
                        $tmpImages[] = $image->getAttribute('src');
                    }
                }

                $strimg = $tmpImages[0] ?? '';

                $images = [
                    [
                        'hiRes' => $strimg,
                        'thumb' => $strimg,
                        'large' => $strimg,
                        'main' => [
                            $strimg => [355, 355],
                            $strimg => [450, 450],
                            $strimg => [425, 425],
                            $strimg => [450, 450],
                            $strimg => [425, 425],
                            $strimg => [466, 466],
                            $strimg => [522, 522],
                            $strimg => [569, 569],
                            $strimg => [679, 679],
                        ],
                        'variant' => 'PT06',
                        'lowRes' => null,
                        'shoppableScene' => null,
                    ],
                ];
            }
        }

        $thumbnails = [];
        $main = '';
        if ($images) {
            foreach ($images as $image) {
                if (isset($image['variant']) && $image['variant'] === 'MAIN' && !$main) {
                    $main = $image['large'];
                }
                $thumbnails[] = $image['large'];
            }
        }

        return $thumbnails;
    }

    private function parseVariants($id, $html){
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $this->xpath = new DOMXPath($dom);
        $variantsParser = new ChapiAmazonVariantsParser($this->xpath);
        $variants = $variantsParser->parse($id, (int)$id, $dom);

        $uniqueVariants = [];
        $alreadySeenNames = [];
        if($variants){
            echo "<script>pData.variants = JSON.parse('" . json_encode($variants) . "');</script>";
        }
        foreach ($variants as $variant) {
            $name = $variant['name'];

            if (!in_array($name, $alreadySeenNames)) {
                $uniqueVariants[] = $variant;
                $alreadySeenNames[] = $name;
            }
        }

        $variants = $uniqueVariants;

        $itera = 0;
        foreach($variants as $variant){
            $itera++;
            if($variant && $variant["type"] == "image"){
                $selectVariants = $this->showColorVariant("", $variant);

// En lugar de addslashes():
                $escapedHtml = json_encode($selectVariants);

// Luego tu script:
                echo "<script>
    var content = $escapedHtml;
    var container = document.querySelector('#color-options');

    if (container) {
        // Agrega (append) al final del contenedor
        container.insertAdjacentHTML('beforeend', content);
    }
</script>";


                echo '<script>
    // Tomamos todos los botones de color
    const colorButtons = document.querySelectorAll(".color-button");
    // Tomamos el input oculto (si lo usamos)
    const hiddenColorInput = document.getElementById("colorInput");

    // Función que marca un botón como seleccionado
    function setSelectedColor(button) {
        // 1. Quitamos el “anillo” (ring) de todos los botones
        colorButtons.forEach((btn) => {
            btn.classList.remove("ring-2", "ring-offset-2", "ring-blue-500");
        });
        // 2. Agregamos el anillo al botón clicado
        button.classList.add("ring-2", "ring-offset-2", "ring-blue-500");

        // 3. Actualizamos el valor del input oculto
        if (hiddenColorInput) {
            hiddenColorInput.value = button.dataset.color;
        }

        const chosenAsin = button.getAttribute("data-sku")
        if (chosenAsin) {
            selectedVariantAsin = chosenAsin;
            updateProduct(chosenAsin, chosenAsin);
        }
    }

    // Asignamos el evento click a cada botón
    colorButtons.forEach((btn, index) => {
        btn.addEventListener("click", () => {
            setSelectedColor(btn);
        });
    });


</script>
';
            }else{
                $selectVariants = $this->showSelectVariant("", $variant);
                $optionSelected = null;
                foreach($variant["options"] as $option){
                    if($option["selected"]){
                        $optionSelected = $option;
                        break;
                    }
                }
                // Convertimos todo el HTML en una cadena JSON válida
                $selectVariants = str_replace('{{ select_name }}', $variant["name"], $selectVariants);
                $escapedHtml = json_encode($selectVariants);


                echo "<script>
    var content = $escapedHtml;
    var container = document.querySelector('#selects');

    if (container) {
        container.insertAdjacentHTML('beforeend', content);
    }
</script>";

                echo '<script>const sizeSelectButton'.$itera.' = document.getElementById("sizeSelectButton-'.$variant["name"].'")
    const sizeSelectLabel'.$itera.' = document.getElementById("sizeSelectLabel-'.$variant["name"].'")
    const sizeOptions'.$itera.' = document.getElementById("sizeOptions-'.$variant["name"].'")
    const hiddenSizeSelect'.$itera.' = document.getElementById("hiddenSizeSelect-'.$variant["name"].'")

    // Mostrar/ocultar opciones
    sizeSelectButton'.$itera.'.addEventListener("click", () => {
    console.log("a");
        sizeOptions'.$itera.'.classList.toggle("hidden")
    })';

    if($optionSelected){

    echo '
    sizeSelectLabel'.$itera.'.textContent = "'.$optionSelected["text"].'";
    // Manejar la selección de una talla
    sizeOptions'.$itera.'.addEventListener("click", (e) => {
        // Verificamos si se hizo click en un li con data-size
        if (e.target.matches("li[data-size]")) {
            const chosenValue = e.target.getAttribute("data-size")
            const chosenText = e.target.textContent
            const chosenAsin = e.target.getAttribute("data-size")
            if (chosenAsin) {
                selectedVariantAsin = chosenAsin;
                updateProduct(chosenAsin, chosenAsin);
            }
            // Actualizamos el texto del botón
            sizeSelectLabel'.$itera.'.textContent = chosenText
            // Actualizamos el select oculto
            hiddenSizeSelect'.$itera.'.value = chosenValue

            // Cerramos el dropdown
            sizeOptions'.$itera.'.classList.add("hidden")
        }
    })';
    }
    echo '
    // (Opcional) Cerrar si se hace click fuera
    document.addEventListener("click", (e) => {
        if (
            !sizeSelectButton'.$itera.'.contains(e.target) &&
            !sizeOptions'.$itera.'.contains(e.target)
        ) {
            sizeOptions'.$itera.'.classList.add("hidden")
        }
    })</script>';
            }
        }
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
}
