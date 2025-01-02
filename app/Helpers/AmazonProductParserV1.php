<?php
namespace App\Helpers;

use App\Helpers\ChunkProductHelper;
use DOMDocument;
use DOMXPath;
use tidy;
use stdClass;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AmazonProductParserV1
{
    protected static $dom;
    private static mixed $element;
    private static mixed $productId;
    private static string $fileName;
    private static mixed $chunk;
    private static mixed $try;
    private static DOMXPath $xpath;

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

    public static function parse($chunk, $productId, $try, &$buffer, &$config, &$globalBuffer)
    {
        self::$fileName = date('YmdHis') . substr((string)microtime(), 2, 6);
        self::$productId = $productId;
        self::$chunk = $chunk;
        $buffer .= $chunk;
        self::$try = $try;
        //echo "AAAA<br>";
        flush();
        foreach($config as &$element){
            self::$element = &$element;
            $contains = self::$element["contains"];

            foreach($contains as $contain){

                if(self::$element["status"] == "done"){
                    continue 2;
                }

                if($contain == "productTitle" && strpos($buffer, $contain) !== false){

                    self::parseHtml($buffer);

                    $title = self::getTitle();

                    return self::returnElement($title, $buffer);
                }elseif(
                    (
                        $contains == "priceblock_ourprice" ||
                        $contains == "a-size-mini" ||
                        $contains == "a-offscreen" ||
                        $contains == "a-price-whole"
                    ) &&
                    strpos($buffer, 'priceblock_ourprice') !== false ||
                    strpos($buffer, 'a-size-mini') !== false ||
                    strpos($buffer, 'a-offscreen') !== false ||
                    strpos($buffer, 'a-price-whole') !== false
                ){
                    self::parseHtml($buffer);

                    $price = self::getPrice();

                    return self::returnElement($price, $buffer);
                }elseif(strpos($buffer, 'imgTagWrapper') !== false){
                    self::parseHtml($buffer);
                    $image = self::getImage();

                    return self::returnElement($image, $buffer);
                }elseif(strpos($buffer, 'twisterDimKeys') !== false){
                    self::parseHtml($buffer, true);

                    $currentAsin = "B07BN7D19Y";
                    $productIdTmp = "1";
                    $resVariants = self::parseVariants($currentAsin, $productIdTmp, self::$xpath);

                    return self::returnElement($resVariants, $buffer);
                }elseif(strpos($buffer, 'variation_color_name') !== false){
                    self::parseHtml($buffer, true);


                    $currentAsin = "B07BN7D19Y";
                    $productIdTmp = "1";
                    $resVariants = self::parseVariants($currentAsin, $productIdTmp, self::$xpath, true);

                    return self::returnElement($resVariants, $buffer);
                }
            }

        }

        //self::createHtmlFile(self::$productId, self::$fileName, self::$chunk, self::$try, "");
    }

    private static function parseHtml($buffer, $generateXpath = false){
        self::$element["status"] = "in_progress";
        // Repara y procesa el HTML
        $cleanHtml = self::repairHtml($buffer);

        if($generateXpath){
            self::$xpath = new DOMXPath(self::$dom);
        }

        // Carga el DOM reparado
        self::initHtmlDom($cleanHtml);
    }


    private static function returnElement($data, &$buffer){
        if($data){
            self::$element["status"] = "done";
            //self::createHtmlFileSuccess(self::$productId, self::$fileName, self::$chunk, "variants_color", self::$try);
            $buffer = "";
            return $data;
        }

        return "";
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
            return "<img src='". $img ."'>";
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
                $currentPrice = $tmpPrice . " | a1";
            }
        }

        foreach ($priceSelectors as $selector) {
            $priceElement = $xpath->query($selector)->item(0);
            if ($priceElement) {
                $tmpPrice = self::parsePrice($priceElement->textContent);
                if ($tmpPrice > $currentPrice) {
                    $currentPrice = $tmpPrice . " |2 " . $selector;
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
                    $currentPrice = $tmpPrice . " |3 " . $selector;
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
                    $currentPrice = $currentPrice  . " | a2";;
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
        return "<div> $" . (float) $price . "</div>";
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

    private static function parseVariants($currentAsin, $productId, $xpath, $color = false): string
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

        return self::formatVariants($variants, $productId, $color);
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
