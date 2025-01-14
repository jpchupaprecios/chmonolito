<?php
namespace App\Helpers;

use DOMDocument;
use DOMXPath;
use tidy;

class AmazonSearchParser
{
    protected static $dom;

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

    public static function parse($chunk, &$usedAsins, &$counter, &$bufferLimited)
    {
        //if (strpos($chunk, 'data-asin="B') !== false) {
        //    $productNodes = $xpath->query('//div[@data-asin and string-length(@data-asin) > 0]');

        if (strpos($chunk, 'data-asin="B') !== false) {
            $counter++;
            $bufferLimited .= $chunk;
        //if(strpos($chunk, 'a-size') !== false){
            // Repara y procesa el HTML
            $cleanHtml = self::repairHtml($chunk);

            // Carga el DOM reparado
            self::initHtmlDom($cleanHtml);

            $xpath = new DOMXPath(self::$dom);
            $productNodes = $xpath->query('//div[@data-asin and string-length(@data-asin) > 0]');
            $products = [];
            foreach($productNodes as $productNode){
                $productDom = new DOMDocument();
                $productDom->appendChild($productDom->importNode($productNode, true));
                $html = $productDom->saveHTML();
                $productXPath = new DOMXPath($productDom);
                $titleElement = $productXPath->query('.//span[contains(@class, "a-size-base-plus")]');
                if(!count($titleElement)){
                    $titleElement = $productXPath->query('.//h2//span');
                }
                $title = "";
                if(count($titleElement)){
                    $titleElement =$titleElement->item(0);
                    $title = trim($titleElement->textContent);
                }
                $image = "";
                $imageAlt = "";
                $imageElement = $productXPath->query('.//img');
                if(count($imageElement)){
                    $imageElement = $imageElement->item(0);
                    if($imageElement){
                        $image = $imageElement->getAttribute('src');
                        $imageAlt = $imageElement->getAttribute('alt');
                    }
                }

                if($imageAlt && !$title){
                    $title = $imageAlt;
                }

                $priceElement = $productXPath->query(
                    './/span[contains(@class, "a-price")]/span[contains(@class, "a-offscreen")]',
                );

                $price = 0;
                if(count($priceElement)){
                    $priceElement = $priceElement->item(0);
                    $price = self::parsePrice($priceElement->textContent);
                }

                $node = $productXPath->query('.//@data-asin');
                $productId = "";
                if(count($node)){
                    $node = $node->item(0);
                    $productId = $node->nodeValue;
                }

                if($price && $image && $productId){
                    if(!in_array($productId, $usedAsins)){
                        $usedAsins[] = $productId;

                        $products[] = [
                            "product_id" => $productId,
                            "price" => $price,
                            "image" => $image,
                            "brand" => "",
                            "title" => $title,
                        ];
                    }
                }

            }

            if(!$products){
                $cleanHtml = self::repairHtml($bufferLimited);

                // Carga el DOM reparado
                self::initHtmlDom($cleanHtml);

                $xpath = new DOMXPath(self::$dom);
                $productNodes = $xpath->query('//div[@data-asin and string-length(@data-asin) > 0]');
                $products = [];
                foreach($productNodes as $productNode) {
                    $productDom = new DOMDocument();
                    $productDom->appendChild($productDom->importNode($productNode, true));
                    $html = $productDom->saveHTML();
                    $productXPath = new DOMXPath($productDom);
                    $titleElement = $productXPath->query('.//span[contains(@class, "a-size-base-plus")]');
                    if (!count($titleElement)) {
                        $titleElement = $productXPath->query('.//h2//span');
                    }
                    $title = "";
                    if (count($titleElement)) {
                        $titleElement = $titleElement->item(0);
                        $title = trim($titleElement->textContent);
                    }
                    $image = "";
                    $imageAlt = "";
                    $imageElement = $productXPath->query('.//img');
                    if (count($imageElement)) {
                        $imageElement = $imageElement->item(0);
                        if ($imageElement) {
                            $image = $imageElement->getAttribute('src');
                            $imageAlt = $imageElement->getAttribute('alt');
                        }
                    }

                    if ($imageAlt && !$title) {
                        $title = $imageAlt;
                    }

                    $priceElement = $productXPath->query(
                        './/span[contains(@class, "a-price")]/span[contains(@class, "a-offscreen")]',
                    );

                    $price = 0;
                    if (count($priceElement)) {
                        $priceElement = $priceElement->item(0);
                        $price = self::parsePrice($priceElement->textContent);
                    }

                    $node = $productXPath->query('.//@data-asin');
                    $productId = "";
                    if (count($node)) {
                        $node = $node->item(0);
                        $productId = $node->nodeValue;
                    }

                    if ($price && $image && $productId) {
                        if(!in_array($productId, $usedAsins)){
                            $usedAsins[] = $productId;

                            $products[] = [
                                "product_id" => $productId,
                                "price" => $price,
                                "image" => $image,
                                "brand" => "",
                                "title" => $title,
                            ];
                        }
                    }
                }

                if($counter == 3){
                    $counter = 0;
                    $bufferLimited = "";
                }
            }

            return $products;

            // Extrae información del DOM
            $title = self::getTitle();
            $html = self::$dom->saveHTML();
            if($title){
                $price = self::getPrice();
                $productId = self::getProductId();
                $image = self::getImage();
                $vendor = "amazon";
                if($price && $image && $productId){
                    return [
                        "product_id" => $productId,
                        "price" => $price,
                        "image" => $image,
                        "brand" => "",
                        "title" => $title,
                    ];
                }else{
                    $a = 1;
                }
            }else{
                $a = 1;
            }

            return null;
        }

        return null;
    }

    protected static function getProductId(): string
    {
        $asin = "";
        $xpath = new DOMXPath(self::$dom);

        $node = $xpath->query('//span[@data-csa-c-item-id]')->item(0);
        if($node){
            $asin = $node->getAttribute('data-csa-c-item-id');
            if($asin){
                $asin = str_replace('amzn1.asin.', '', $asin);
                $asin = str_replace('amzn1.deal.', '', $asin);
                $map = explode('.', $asin);
                if(count($map) > 1){
                    $asin = $map[0];
                }
                $map2 = explode(':', $asin);
                if(count($map2) > 1){
                    $asin = $map2[0];
                }
            }
        }
        return $asin;
    }

    protected static function getImage(): string
    {
        $xpath = new DOMXPath(self::$dom);
        $mainImage = $xpath->query('.//img[contains(@class, "s-image")]')->item(0);
        $img = $mainImage ? (string) $mainImage->getAttribute('src') : '';
        if($img){
            return $img;
        }

        return "";
    }

    protected static function getTitle(): ?string
    {
        if (!self::$dom) {
            return null;
        }

        $xpath = new DOMXPath(self::$dom);

        // Intenta encontrar el título
        $titleElement = $xpath->query('//h2[contains(@class, "a-size-mini")]')->item(0);
        $titleElement2 = $xpath->query('//h2[contains(@class, "a-size-base-plus")]')->item(0);

        // Extrae el texto del título si está disponible
        if ($titleElement) {
            $title = trim($titleElement->textContent);
            return htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        }

        if ($titleElement2) {
            $title = trim($titleElement2->textContent);
            return htmlspecialchars($title, ENT_QUOTES, 'UTF-8');
        }

        return null;
    }

    protected static function getPrice(): string
    {
        $xpath = new DOMXPath(self::$dom);
        $priceElement = $xpath->query(
            './/span[contains(@class, "a-price")]/span[contains(@class, "a-offscreen")]',
        )->item(0);

        if ($priceElement) {
            return self::parsePrice($priceElement->textContent);
        }

        $priceElements = $xpath->query('.//span[contains(@class, "a-color-base")]');
        foreach ($priceElements as $txt) {
            if (strpos($txt->textContent, '$') !== false) {
                return self::parsePrice($txt->textContent);
            }
        }

        return 0;
    }

    private static function parsePrice(string $price): string
    {
        $price = str_replace(['US$', '$', 'US', ','], '', $price);
        return (float) $price . "";
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
