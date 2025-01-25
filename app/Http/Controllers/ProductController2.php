<?php

namespace App\Http\Controllers;

use App\Helpers\AmazonProductParser;
use App\Parsers\Chapi\Amazon\Product\Variants\ChapiAmazonVariantsParser;
use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
use DOMDocument;
use DOMXPath;
use App\Parsers\Chapi\Amazon\Complete\Product\ChapiAmazonProductDetailParser;
class ProductController2 extends Controller
{
    protected const COOKIE_PATH = 'app/';

    private function showLayout(){
        $layoutStart = file_get_contents(resource_path('views/layouts/layoutStart.blade.php'));
        $layoutStart = $this->showMarquee($layoutStart);
        $layoutStart = $this->showHeader($layoutStart);
        $layoutStart = $this->showCategories($layoutStart);
        $layoutStart = $this->showSearchWrapper($layoutStart);
        $layoutStart = $this->showFav($layoutStart);
        $layoutStart = $this->showBreadcrumb($layoutStart);
        $layoutStart = $this->showQuantityControls($layoutStart);

        return $layoutStart;
    }

    private function showSearchWrapper($layoutStart){
        $searchWrapper = file_get_contents(resource_path('views/pages/details/index2.blade.php'));
        $searchBar = file_get_contents(resource_path('views/components/search.blade.php'));
        $layoutStart = str_replace('{{ //SEARCH}}', $searchBar, $layoutStart);
        $layoutStart = str_replace('{{ //CONTENT}}', $searchWrapper, $layoutStart);
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
        foreach($variants as $variant){

            $selected = ($variant["selected"]) ? ' ring-blue-500 ring-2 ring-offset-2' : '';
            $colors .=
            "<img " .
                'data-sku="'.$variant["sku"].'"'.
                'class="color-button w-16 border-2 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 border-gray-300 '.$selected.'"'.
                'src="'.$variant["img"].'" />';
        }

        $colorsDiv = str_replace('<!-- color_variants -->', $colors, $colorsDiv);
        return $colorsDiv;
    }

    public function index(Request $request, $id, $vendor)
    {
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        echo $this->showLayout($id);
        flush();
        $global = "";
        $url = 'https://www.amazon.com/dp/' . $id;
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'session-id=145-2848617-2390738; ...';

        // **Lista de proxies a usar**
        $proxies = [
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

        $multiCurl = curl_multi_init();
        $handles = [];

        foreach ($proxies as $proxy) {
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
                CURLOPT_PROXY => $proxy['host'],
                CURLOPT_PROXYPORT => $proxy['port'],
                CURLOPT_PROXYUSERPWD => $proxy['user'] . ':' . $proxy['pass'],
                CURLOPT_BUFFERSIZE => 1024,
                CURLOPT_WRITEFUNCTION => function ($curl, $chunk) use ($id) {
                    static $global = "";
                    static $formVariants = "";
                    static $alreadyVariants = false;
                    static $variantsForm = false;
                    static $variantsDiv = false;
                    static $thumbsChunks = "";
                    static $imagesThumb = null;

                    // **Procesar imágenes en tiempo real**
                    if (!$imagesThumb && strpos($chunk, '[{"hiRes') !== false) {
                        $thumbsChunks .= $chunk;
                        $imagesThumb = self::getImages($thumbsChunks);
                        if ($imagesThumb) {
                            echo "<script>pData.thumbs = JSON.parse('" . addslashes(json_encode($imagesThumb)) . "');</script>";
                            flush();
                        }
                    }

                    // **Parseo de variantes**
                    if (!$alreadyVariants) {
                        if ($formVariants) $formVariants .= $chunk;
                        if (strpos($chunk, 'twister-plus-inline-twister') !== false) {
                            $formVariants .= $chunk;
                            $variantsDiv = true;
                        }
                        if (strpos($chunk, 'form id="twiste') !== false) {
                            $formVariants .= $chunk;
                            $variantsForm = true;
                        }
                        if ($variantsDiv && strpos($chunk, 'dp-cif aok-hidden') !== false) {
                            self::parseVariants($id, $formVariants);
                            $alreadyVariants = true;
                        }
                        if ($variantsForm && strpos($chunk, '</form') !== false) {
                            self::parseVariants($id, $formVariants);
                            $alreadyVariants = true;
                        }
                    }

                    // **Parseo general del producto**
                    $global .= $chunk;
                    $parsedProducts = AmazonProductParser::processHtmlChunks($chunk, $global);

                    if ($parsedProducts) {
                        foreach ($parsedProducts as $key => $value) {
                            switch ($key) {
                                case "price":
                                    echo "<script>pData.price = '" . addslashes($value) . "';</script>";
                                    break;
                                case "title":
                                    echo "<script>pData.title = '" . addslashes($value) . "';</script>";
                                    break;
                                case "image":
                                    echo "<script>pData.image = '" . addslashes($value) . "';</script>";
                                    break;
                                case "rating":
                                    echo "<script>pData.rating = '" . addslashes($value) . "';</script>";
                                    break;
                            }
                            flush();
                        }
                    }

                    return strlen($chunk);
                }
            ]);

            curl_multi_add_handle($multiCurl, $curl);
            $handles[] = $curl;
        }

        // **Ejecutar en paralelo**
        do {
            $status = curl_multi_exec($multiCurl, $active);
            curl_multi_select($multiCurl);
        } while ($active && $status == CURLM_OK);

        // **Cerrar conexiones**
        foreach ($handles as $curl) {
            curl_multi_remove_handle($multiCurl, $curl);
            curl_close($curl);
        }

        curl_multi_close($multiCurl);

        // **Procesar datos finales**
        $chapiAmazonProductDetailParser = new ChapiAmazonProductDetailParser($cookie);
        $data = $chapiAmazonProductDetailParser->parse(["result" => $global], "amazon", $id, $cookie);

        $dom = new DOMDocument();
        $dom->loadHTML($global);
        $xpath = new DOMXPath($dom);
        $variantsParser = new ChapiAmazonVariantsParser($xpath);
        $variants = $variantsParser->parse($id, (int)$id, $dom);

        if ($data && $variants) {
            $data->variants = $variants;
        }

        if ($data) {
            echo "<script>pDataC = JSON.parse('" . addslashes(json_encode($data)) . "');</script>";
            flush();
        }

        echo file_get_contents(resource_path('views/layouts/layoutEnd.blade.php'));
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
            echo "<script>pData.variants = JSON.parse('" . addslashes(json_encode($variants)) . "');</script>";
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
    }

    // Asignamos el evento click a cada botón
    colorButtons.forEach((btn, index) => {
        btn.addEventListener("click", () => {
            setSelectedColor(btn);
        });
    });

    // (Opcional) Seleccionar por defecto el primer color,
    // o cualquier lógica inicial que quieras.
    if (colorButtons.length > 0) {
        setSelectedColor(colorButtons[0]);
    }
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
