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

    public static function parse($chunk)
    {
        if(strpos($chunk, 'a-size') !== false){
            // Repara y procesa el HTML
            $cleanHtml = self::repairHtml($chunk);

            // Carga el DOM reparado
            self::initHtmlDom($cleanHtml);

            // Extrae información del DOM
            $title = self::getTitle();
            if($title){
                $price = self::getPrice();
                $productId = self::getProductId();
                $image = self::getImage();
                if($price && $image && $productId){
                    return "<div>" .$productId.$image .  $title . $price . "</div>";
                }
            }

            return "";
        }
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
                $asin = "<a><a href='/product/".$asin."/amazon'>" . $asin . "</a></h1>";
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
            return "<img src='". $img ."'>";
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
            return "<div>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</div>";
        }

        if ($titleElement2) {
            $title = trim($titleElement2->textContent);
            return "<div>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</div>";
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
        return "<div> $" . (float) $price . "</div>";
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
