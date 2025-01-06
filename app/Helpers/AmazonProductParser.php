<?php
namespace App\Helpers;

use App\Helpers\ChunkProductHelper;
use DOMDocument;
use DOMXPath;
use tidy;
use stdClass;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AmazonProductParser
{
    protected static $dom;

    protected static $config = [
        "title" => [
            "contains" => [
                'productTitle'
            ],
        ],
        "price" => [
            "contains" => [
                'corePrice_feature_div'
            ],
        ],
        "image" => [
            "contains" => [
                'imgTagWrapperId'
            ],
        ],
        "variant" => [
            "contains" => [
                'twisterDimKeys'
            ],
        ],
        "variant_color" => [
            "contains" => [
                'variation_color_name'
            ],
        ],
    ];

    private static function initHtmlDom($html)
    {
        try {
            libxml_use_internal_errors(true);
            self::$dom = new DOMDocument();

            // Reparar HTML antes de cargarlo
            $cleanHtml = self::repairHtml($html);

            self::$dom->loadHTML($cleanHtml);
        } catch (\Exception $e) {
            return null;
        }
    }

    public static function processHtmlChunks($chunk, &$buffer, &$datas, $productId)
    {
        // Acumular el chunk en el buffer
        $buffer .= $chunk;

        foreach (self::$config as $key => $elementConfig) {
            // Si ya se procesó este elemento, saltarlo
            if ($datas[$key]["status"] === "done") {
                continue;
            }

            // Verificar si el inicio está presente
            $startDetected = false;
            if(isset($elementConfig['contains'])){
                foreach($elementConfig['contains'] as $contains){
                    $startDetected = strpos($buffer, $contains) !== false;
                    if($startDetected){
                        break;
                    }
                }
            }

            if (!$startDetected) {
                $robot = strpos($buffer, "not a robot") !== false;
                if(!$robot){
                    $a = 1;
                }
                continue; // Si no encontramos el inicio, esperamos más chunks
            }

            // Extraer el contenido completo del elemento
            $fullHtml = $buffer;

            // Reparar el HTML
            libxml_use_internal_errors(true);
            $dom = new DOMDocument();
            $dom->loadHTML(self::repairHtml($fullHtml));
            self::$dom = $dom;

            // Procesar según el elemento
            $content = "";
            switch ($key) {
                case "title":
                    $content = self::getTitle();
                    break;
                case "price":
                    $content = self::getPrice();
                    break;
                case "image":
                    $content = self::getImage();
                    break;
                case "variant_color":
                    $currentAsin = $productId;
                    $productIdTmp = "1";
                    $xpath = new DOMXPath(self::$dom);
                    $content = self::parseVariants($currentAsin, $productIdTmp, $xpath);
                    break;
                case "variant":
                    $currentAsin = $productId;
                    $productIdTmp = "1";
                    $xpath = new DOMXPath(self::$dom);
                    $content = self::parseVariants($currentAsin, $productIdTmp, $xpath);
                    break;
                default:
                    /*$xpaths = is_array($elementConfig['xpath']) ? $elementConfig['xpath'] : [$elementConfig['xpath']];
                    $xpath = new DOMXPath($dom);
                    foreach ($xpaths as $selector) {
                        $node = $xpath->query($selector)->item(0);
                        if ($node) {
                            $content = trim($node->textContent);
                            break;
                        }
                    }*/
                    break;
            }

            if ($content) {
                // Almacenar el contenido en $datas
                $datas[$key]["content"] = $content;
                $datas[$key]["status"] = "done";

                // Limpiar el buffer hasta después del elemento procesado
                //$buffer = substr($buffer, $endPos + strlen($elementConfig['end']));
                $buffer = "";
                return [$key => $content]; // Retornar el contenido procesado
            }
        }

        return null; // Ningún elemento procesado aún
    }

    private static function getTitle(): string
    {
        if (!self::$dom) {
            return "";
        }


        $xpath = new DOMXPath(self::$dom);

        $titleElement = $xpath->query('//span[@id="productTitle"]')->item(0);
        $titleElement = $titleElement ? trim($titleElement->textContent) : '';

        if(!$titleElement){

        }

        return str_replace('%', ' Porciento ', $titleElement);
    }

    protected static function getImage(): string
    {
        if (!self::$dom) {
            return "";
        }

        $xpath = new DOMXPath(self::$dom);
        $mainImage = $xpath->query('//*[@id="imgTagWrapperId"]//img')->item(0);
        $img = $mainImage ? (string) $mainImage->getAttribute('src') : '';
        if($img){
            return $img;
        }

        if(!$img){

        }

        return "";
    }

    private static function getPrice(): string
    {
        if (!self::$dom) {
            return "";
        }
        $currentPrice = 0.0;

        $xpath = new DOMXPath(self::$dom);

        $priceSelectors = [
            '//*[@id="priceblock_ourprice"]',
            '//form//*[@class="a-button a-button-selected"]//*[@class="a-size-mini"]',
            '//*[@id="dp-container"]//*[@id="ppd"]//*[@class="a-price"]//*[@class="a-offscreen"]',
            '//*[@id="dp-container"]//*[@id="ppd"]//*[contains(@class, "a-price")]//*[@class="a-offscreen"]',
            '//*[@id="centerCol"]//*[contains(@class, "a-price-whole")]'
        ];

        $priceElement = $xpath->query('.//*[@class="a-section a-spacing-none a-padding-none"]//span[contains(@class, "a-price")]//span[contains(@class, "a-offscreen")]')->item(0);
        if ($priceElement) {
            $tmpPrice = self::parsePrice($priceElement->textContent);
            if ($tmpPrice > $currentPrice) {
                $currentPrice = $tmpPrice;
            }
        }

        foreach ($priceSelectors as $selector) {
            $priceElement = $xpath->query($selector)->item(0);
            if ($priceElement) {
                $tmpPrice = self::parsePrice($priceElement->textContent);
                if ($tmpPrice > $currentPrice) {
                    $currentPrice = $tmpPrice;
                }
            }
        }

        if ($currentPrice) {
            return $currentPrice;
        }

        $priceSelectors = [
            '//div[contains(@class, "a-price-range")]//*[@class="a-price"]//*[@class="a-offscreen"]',
            '//*[@id="buyBoxAccordion"]//*[contains(@class, "a-price")]//*[@class="a-offscreen"]',
            '//*[@id="desktop_buybox"]//*[contains(@class, "a-price")]//*[@class="a-offscreen"]',
        ];

        foreach ($priceSelectors as $selector) {
            $priceElement = $xpath->query($selector)->item(0);
            if ($priceElement) {
                $tmpPrice = self::parsePrice($priceElement->textContent);
                if ($tmpPrice > $currentPrice) {
                    $currentPrice = $tmpPrice;
                }
            }
        }

        if ($currentPrice) {
            return $currentPrice;
        }

        if ($currentPrice < 1) {
            $priceRanges = $xpath->query('//*[@id="corePrice_desktop"][.//*[contains(@class, "a-price")]]//*[@class="a-offscreen"]');

            foreach($priceRanges as $priceRange){
                if($priceRange && $priceRange->textContent){
                    $currentPrice = self::parsePrice($priceRange->textContent);
                    $currentPrice = $currentPrice;
                    break;
                }
            }
        }

        if(!$currentPrice){
            $descriptionHtml = self::$dom->saveHTML();

        }

        return $currentPrice;
    }

    private static function parsePrice(string $price): string
    {
        $price = str_replace(['US$', '$', 'US', ','], '', $price);
        return (float) $price;
    }

    private static function repairHtml(string $html): string
    {
        // Convierte a UTF-8 si no lo está
        if (!mb_check_encoding($html, 'UTF-8')) {
            $html = mb_convert_encoding($html, 'UTF-8', 'auto');
        }

        // Usa tidy si está disponible
        if (extension_loaded('tidy')) {
            $config = [
                'indent' => true,
                'output-xhtml' => true,
                'wrap' => 200,
                'input-encoding' => 'utf8',
                'output-encoding' => 'utf8',
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

    /* VARIANTS */

    private static function parseVariants($currentAsin, $productId, $xpath, $color = false): array
    {
        $variantNames = self::getVariantNames($xpath);

        $matchingDivs = self::getMatchingDivs($xpath);

        if (empty($variantNames)) {
            $variantNames = $matchingDivs;
        }

        $variants = [];
        foreach ($variantNames as $name) {
            $variantBlock = self::getVariantBlock($name, $xpath);
            $variantType = 'default';
            $title = '';

            if ($variantBlock) {
                $title = self::getVariantTitle($variantBlock, $xpath);
                if (self::hasImages($variantBlock, $xpath)) {
                    $variantType = 'image';
                }

                $variantData = [
                    'name' => $name,
                    'type' => $variantType,
                    'title' => utf8_encode($title),
                    'options' => self::processVariants($variantBlock, $currentAsin, $variantType, $xpath, $color),
                ];

                $variants[] = $variantData;
            }
        }

        return $variants;//self::formatVariants($variants, $productId, $color);
    }

    private static function getVariantNames($xpath): array
    {
        $xpath->query('//span[@name="a-button-inner"]')->item(0);
        $variantNamesElement = $xpath->query('//input[@name="twisterDimKeys"]')->item(0);
        $result = $variantNamesElement ? explode(',', $variantNamesElement->getAttribute('value')) : [];

        if($result){
            return $result;
        }

        return [];

    }

    private static function getMatchingDivs($xpath): array
    {
        $matchingDivs = [];
        $divs = $xpath->query('//div');

        foreach ($divs as $div) {
            $id = $div->getAttribute('id');
            if ($id && (preg_match('/^inline-twister-row-[a-zA-Z_]+$/', $id) || preg_match('/^variation_[a-zA-Z_]+$/', $id))) {
                $modifiedId = preg_replace('/^(inline-twister-row-|variation_)/', '', $id);
                $matchingDivs[] = $modifiedId;
            }
        }

        return $matchingDivs;
    }

    private static function getVariantBlock(string $name, $xpath)
    {
        $variantBlock = $xpath->query("//div[@id='variation_$name']")->item(0);
        if (!$variantBlock) {
            $variantBlock = $xpath->query("//div[@id='inline-twister-row-$name']")->item(0);
        }
        return $variantBlock;
    }

    private static function getVariantTitle($variantBlock, $xpath): string
    {
        $titleElement = $xpath->query('.//label | .//span[contains(@class, "dimension-text")]', $variantBlock)->item(0);
        if ($titleElement) {
            $title = trim(($titleElement->textContent));
            if (strpos($title, ':') !== false) {
                $title = trim(explode(':', $title)[0]);
            }
            return trim($title, ':');
        }
        return '';
    }

    private static function hasImages($variantBlock, $xpath): bool
    {
        return $xpath->query('.//img', $variantBlock)->length > 0;
    }

    private static function processVariants($variantBlock, string $currentAsin, string $variantType = 'default', $xpath, $color): array
    {
        $variantData = [];
        $options = $xpath->query('.//li | .//option', $variantBlock);

        foreach ($options as $option) {
            $asin = $option->getAttribute('data-csa-c-item-id') ? $option->getAttribute('data-csa-c-item-id') : self::getAsinFromDataDpUrl($option);

            if(!$asin){
                $asin = $option->getAttribute('value') ?? '';
                if($asin === "-1"){
                    continue;
                }
                if($asin && strpos($asin, ',') !== false){
                    $asin = explode(',', $asin)[1];
                }
            }

            if(!$asin){
                continue;
            }

            $isAvailable = strpos($option->getAttribute('class'), 'swatchUnavailable') === false && strpos($option->getAttribute('class'), 'dropdownUnavailable') === false;

            $optionData = [
                'sku' => $asin,
                'available' => $isAvailable,
                'text' => '',
                'img' => null,
                'selected' => $currentAsin === $asin,
            ];

            if ($variantType === 'image') {
                $imgElement = $xpath->query('.//img', $option)->item(0);
                if ($imgElement) {
                    $optionData['img'] = $imgElement->getAttribute('src');
                    $optionData['text'] = $imgElement->getAttribute('alt');
                }
            } else {
                $buttonElement = $xpath->query('.//button', $option)->item(0);
                $spanElement = $xpath->query('.//span[contains(@class, "swatch-title-text-display")]', $option)->item(0);
                if ($buttonElement) {
                    $optionData['text'] = trim(($buttonElement->textContent));
                } elseif ($spanElement) {
                    $optionData['text'] = trim(($spanElement->textContent));
                } else {
                    $optionData['text'] = trim(($option->textContent));
                }
            }

            $optionData['text'] = trim(str_replace("\n", '', $optionData['text']));

            if ($optionData['text'] && $optionData['sku']) {
                $variantData[] = $optionData;
            }
        }

        return $variantData;
    }

    private static function getAsinFromDataDpUrl($option): ?string
    {
        $dataDpUrl = $option->getAttribute('data-dp-url');
        if ($dataDpUrl) {
            preg_match('/\/dp\/([^\/]+)/', $dataDpUrl, $matches);
            return $matches[1] ?? null;
        }
        return null;
    }

    private static function formatVariants(array $variants, int $productId, $color): string
    {
        $result = "";
        foreach ($variants as $variantResult) {
            $variantsGroup = new \stdClass();
            $variantsGroup->product_details_id =  $productId;
            $type = $variantResult['type'] === 'image' ? 'color' : 'default';

            if(!$color){
                if(!$result){
                    if($variantResult['title']){
                        $result .= "<label>". $variantResult['title'] ."</label><select>";
                    }
                }
            }else{
                if(!$result){
                    if($variantResult['title']){
                        $result .= "<div class='images-variants'><label>". $variantResult['title'] ."</label>";
                    }
                }
            }

            foreach ($variantResult['options'] as $variant) {
                if (is_array($variant)) {
                    if (trim(strtolower($variant['text'])) === 'seleccionar' || trim(strtolower($variant['text'])) === 'select') {
                        continue;
                    }

                    $selected = ($variant['selected']) ? "selected" : "";
                    $available = ($variant['available']) ? "available" : "";

                    if($color){
                        $type = "image";
                        if($variant["img"]){
                            $result .= "<img "  . $available  ." " . $selected . " data-sku='".$variant['sku']."' src='".$variant["img"]."'>";
                        }
                    }else{
                        $type = "default";
                        $result .= "<option " . $available . " " . $selected. " value='".$variant['sku']."'>". $variant['text'] ."</option>";
                    }

                }
            }

            if($result){
                if(!$color){
                    if($result){
                        $result .= "</select>";
                    }
                }else{
                    if($result){
                        $result .= "</div>";
                    }
                }
            }
        }

        return $result;
    }

    private static function createHtmlFile($folderName, $fileName, $content, $ext, $instance)
    {
        // Nombre del archivo HTML
        $fileName = $fileName . '.html';

        // Ruta del directorio padre
        $parentPath = storage_path("app/skus/{$folderName}/{$folderName}_{$ext}/try");

        if($instance){
            $parentPath = storage_path("app/skus/{$folderName}/{$folderName}_{$ext}/try/{$instance}");
        }

        // Crea el directorio padre si no existe
        if (!File::exists($parentPath)) {
            File::makeDirectory($parentPath, 0777, true);
        }

        // Guarda el archivo
        $filePath = "{$parentPath}/{$fileName}";
        File::put($filePath, $content);

        return true;
    }


    private static function createHtmlFileSuccess($folderName, $fileName, $content, $instance, $ext)
    {
        // Nombre del archivo HTML
        $fileName = $fileName . '.html';

        // Ruta del directorio padre
        $parentPath = storage_path("app/skus/{$folderName}/{$folderName}_{$ext}");

        // Crea el directorio padre si no existe
        if (!File::exists($parentPath)) {
            File::makeDirectory($parentPath, 0777, true);
        }

        // Ruta del subdirectorio de éxito
        $successPath = "{$parentPath}/{$folderName}_{$ext}_success_{$instance}";

        // Crea el subdirectorio de éxito si no existe
        if (!File::exists($successPath)) {
            File::makeDirectory($successPath, 0777, true);
        }

        // Guarda el archivo
        $filePath = "{$successPath}/{$fileName}";
        File::put($filePath, $content);

        return true;
    }

}
