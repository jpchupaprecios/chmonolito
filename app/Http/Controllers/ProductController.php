<?php

namespace App\Http\Controllers;

use App\Helpers\AmazonProductParser;
use App\Parsers\Chapi\Amazon\Product\Variants\ChapiAmazonVariantsParser;
use Illuminate\Http\Request;
use App\Helpers\AmazonSearchParser;
use DOMDocument;
use DOMXPath;
class ProductController extends Controller
{
    protected const COOKIE_PATH = 'app/';

    private function showLayout(){
        $layoutStart = file_get_contents(resource_path('views/layouts/layoutStart.blade.php'));
        $layoutStart = $this->showMarquee($layoutStart);
        $layoutStart = $this->showHeader($layoutStart);
        $layoutStart = $this->showCategories($layoutStart);
        $layoutStart = $this->showSearchWrapper($layoutStart);
        $layoutStart = $this->showFav($layoutStart);

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

        //return view('pages.details.index');
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: no-cache');
        header('X-Accel-Buffering: no');
        header('Transfer-Encoding: chunked');
        header('Connection: keep-alive');

        echo $this->showLayout();

        //\App\Models\ChunkProductLog::truncate();
        $url = 'https://www.amazon.com/dp/' . $id;
        $cookieName = date('Y-m-d') . '-amazon';
        $cookiePath = storage_path(self::COOKIE_PATH . $cookieName . '.txt');
        $cookie = 'session-id=145-2848617-2390738; i18n-prefs=USD; skin=noskin; ubid-main=134-0166731-7376829; lc-main=es_US; session-id-time=2082787201l; aws-mkto-trk=id%3A112-TZM-766%26token%3A_mch-aws.amazon.com-1714592830565-97216; aws_lang=en; AMCVS_7742037254C95E840A4C98A6%40AdobeOrg=1; s_cc=true; aws-target-visitor-id=1731609730515-285914.44_0; remember-account=false; regStatus=registered; aws-account-alias=chupaprecios; aws-target-data=%7B%22support%22%3A%221%22%7D; x-main=cU7cDGCKZ8IpQIFcR7d0MFhw2ZDY@XWQwHGhPVLILRsw2?9FRuYU?@Rl5rgvnkaS; AMCV_7742037254C95E840A4C98A6%40AdobeOrg=1585540135%7CMCIDTS%7C20069%7CMCMID%7C64029980295631160031792846785696741143%7CMCAAMLH-1734547835%7C4%7CMCAAMB-1734547835%7CRKhpRz8krg2tLO6pguXWp5olkAcUniQYPHaMWWgdJ3xzPWQmdj0y%7CMCOPTOUT-1733950235s%7CNONE%7CMCAID%7CNONE%7CMCSYNCSOP%7C411-20071%7CvVersion%7C4.4.0; aws-userInfo=%7B%22arn%22%3A%22arn%3Aaws%3Aiam%3A%3A250933440275%3Auser%2Fjotapey%22%2C%22alias%22%3A%22chupaprecios%22%2C%22username%22%3A%22jotapey%22%2C%22keybase%22%3A%22%22%2C%22issuer%22%3A%22http%3A%2F%2Fsignin.aws.amazon.com%2Fsignin%22%2C%22signinType%22%3A%22PUBLIC%22%7D; session-token=/tJ+/CCt3ecWOLDRhCG/tL8xrdIkyi2Bd5fsikNKnANA+8voKSaAeXwFQivieZO8MBe86nF3tV7tR0VX9pEhifgTLs0jr5t1fXW1nNQ/CPIrpUR7xm7EMO6EiraGX1pLa/6vTltsIDOotD331zIeTwNugbuh6jjtEfFo84BNqJeGas4qighci3peTngb/fiMy/qfX8RL6dFyM0Ilz7mokL30aoEH7vQnG7bJvkFZqqNJyUl1sEqDrzpO4QzD8Xm965+zr+F5DHHtIsGqtOS60TTuTctJCugnvIljH1nLMKqxmHXlg4rvvj2l+glc8kKbKXK/9Yaj7JfUIKQAoahiPGVHThDY8LM8mClX1pCOTi/mrkddBKKeu7qtojwPMjVz; csm-hit=tb:s-HWT4HN3DM66R21GM69TK|1734878749583&t:1734878751211&adb:adblk_no; amp_389c1b=3961e650-93a6-45e8-9540-eca24b1e2495...1ifncffmi.1ifnd9r6u.0.0.0';

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

        $global = "";
        $formVariants = "";
        $alreadyVariants = false;
        $variantsForm = false;
        $variantsDiv = false;
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

            CURLOPT_PROXY => $proxyHost, // Proxy host
            CURLOPT_PROXYPORT => $proxyPort, // Proxy port
            CURLOPT_PROXYUSERPWD => $proxyUser . ':' . $proxyPass, // Proxy authentication

            CURLOPT_BUFFERSIZE => 1024, // Reduce el tamaño del buffer de cURL
            CURLOPT_WRITEFUNCTION => function ($curl, $chunk) use (&$buffer, &$datas, $id, &$global, &$formVariants, &$alreadyVariants, &$variantsForm, &$variantsDiv) {
                // Supongamos que parse() retorna un array de productos
                if(!$alreadyVariants){

                    if($formVariants) {
                        $formVariants .= $chunk;
                    }

                    if(strpos($chunk, 'twister-plus-inline-twister') !== false){
                        $formVariants .= $chunk;
                        $variantsDiv = true;
                    }

                    if(strpos($chunk, 'form id="twiste') !== false){
                        $formVariants .= $chunk;
                        $variantsForm = true;
                    }

                    if($variantsDiv){
                        if($formVariants && strpos($chunk, 'dp-cif aok-hidden') !== false){
                            $formVariants .= $chunk;
                            $this->parseVariants($id, $formVariants);
                            $alreadyVariants = true;
                            $formVariants = "";
                        }
                    }

                    if($variantsForm){
                        if($formVariants && strpos($chunk, '</form') !== false){
                            $formVariants .= $chunk;
                            $this->parseVariants($id, $formVariants);
                            $alreadyVariants = true;
                            $formVariants = "";
                        }
                    }
                }

                $global .= $chunk;
                $parsedProducts = AmazonProductParser::processHtmlChunks($chunk, $buffer, $datas, $id);

                if ($parsedProducts && is_countable($parsedProducts) && count($parsedProducts) > 0) {
                    // Iteras sobre cada producto y renderizas la vista product.blade.php
                    if(key($parsedProducts) == "price"){
                        // Extraer el valor
                        $price = $parsedProducts['price'];

                        // Mandar un script que actualice la clase .product-data-price
                        echo "<script>
        document.querySelector('.product-data-price').textContent = '" . addslashes($price) . "';
    </script>";
                    }elseif(key($parsedProducts) == "title"){
                        $title = $parsedProducts['title'];

                        echo "<script>
        document.querySelector('.product-data-title').textContent = '" . addslashes($title) . "';
    </script>";
                    }elseif(key($parsedProducts) == "image"){
                        $imageUrl = $parsedProducts['image'];

                        echo "<script>
        const imgEl = document.querySelector('.product-data-image');
        imgEl.src = '" . addslashes($imageUrl) . "';
        imgEl.alt = 'Imagen del producto';
    </script>";
                    }elseif(key($parsedProducts) == "rating"){
                        $rating = $parsedProducts['rating'];

                        echo "<script>
        document.querySelector('.product-data-rating').textContent = '" . addslashes($rating) . "';
    </script>";
                    }elseif (key($parsedProducts) === "variant") {
                        $variants = $parsedProducts['variant'];
                        if ($variants) {
                            // Generamos el HTML base con file_get_contents y str_replace
                            /*$selectVariants = $this->showSelectVariant("", $variants[0]);

                            // Convertimos todo el HTML en una cadena JSON válida
                            $escapedHtml = json_encode($selectVariants);

                            // Inyectamos en el DOM con un <script> usando la variable JS
                            echo "<script>
            var content = $escapedHtml;
            document.querySelector('#selects').innerHTML = content;
        </script>";

                            echo '<script>const sizeSelectButton = document.getElementById("sizeSelectButton")
    const sizeSelectLabel = document.getElementById("sizeSelectLabel")
    const sizeOptions = document.getElementById("sizeOptions")
    const hiddenSizeSelect = document.getElementById("hiddenSizeSelect")

    // Mostrar/ocultar opciones
    sizeSelectButton.addEventListener("click", () => {
        sizeOptions.classList.toggle("hidden")
    })

    // Manejar la selección de una talla
    sizeOptions.addEventListener("click", (e) => {
        // Verificamos si se hizo click en un li con data-size
        if (e.target.matches("li[data-size]")) {
            const chosenSize = e.target.getAttribute("data-size")
            // Actualizamos el texto del botón
            sizeSelectLabel.textContent = chosenSize
            // Actualizamos el select oculto
            hiddenSizeSelect.value = chosenSize

            // Cerramos el dropdown
            sizeOptions.classList.add("hidden")
        }
    })

    // (Opcional) Cerrar si se hace click fuera
    document.addEventListener("click", (e) => {
        if (
            !sizeSelectButton.contains(e.target) &&
            !sizeOptions.contains(e.target)
        ) {
            sizeOptions.classList.add("hidden")
        }
    })</script>';*/

                        }
                    }
                    elseif (key($parsedProducts) == "variant_color") {
                        $variants = $parsedProducts['variant_color'];
                        if($variants){
                            /*$selectVariants = $this->showColorVariant("", $variants[0]);

// En lugar de addslashes():
                            $escapedHtml = json_encode($selectVariants);

// Luego tu script:
                            echo "<script>
    var content = $escapedHtml;
    document.querySelector('#color-options').innerHTML = content;
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
';*/
                        }
                    }


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

        if(!$formVariants){
            $a = 1;
        }
        curl_close($curl);

        // Finalizar la página HTML
        $endLayout = file_get_contents(resource_path('views/layouts/layoutEnd.blade.php'));
        $footer = file_get_contents(resource_path('views/components/footer.blade.php'));
        $endLayout = str_replace('{{ //FOOTER}}', $footer, $endLayout);
        echo $endLayout;
        flush(); // Asegurarse de enviar el contenido final
    }

    private function parseVariants($id, $html){
        $dom = new DOMDocument();
        $dom->loadHTML($html);
        $this->xpath = new DOMXPath($dom);
        $variantsParser = new ChapiAmazonVariantsParser($this->xpath);
        $variants = $variantsParser->parse($id, (int)$id, $dom);

        $uniqueVariants = [];
        $alreadySeenNames = [];

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
    })

    // Manejar la selección de una talla
    sizeOptions'.$itera.'.addEventListener("click", (e) => {
        // Verificamos si se hizo click en un li con data-size
        if (e.target.matches("li[data-size]")) {
            const chosenSize = e.target.getAttribute("data-size")
            // Actualizamos el texto del botón
            sizeSelectLabel'.$itera.'.textContent = chosenSize
            // Actualizamos el select oculto
            hiddenSizeSelect'.$itera.'.value = chosenSize

            // Cerramos el dropdown
            sizeOptions'.$itera.'.classList.add("hidden")
        }
    })

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
