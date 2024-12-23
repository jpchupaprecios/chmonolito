<?php

namespace App\Parsers\Chapi\Ebay\Product\ExtendedDetails;
use App\Models\ExtendedDetails\ExtendedDetails;
use App\Parsers\Chapi\Interfaces\ExtendedDetailsParserInterface;
use App\Helpers\FixHtml;

class ChapiEbayExtendedDetailsParser implements ExtendedDetailsParserInterface
{

    //https://www.amazon.com/dp/B0CZ3NSNSS/ref=syn_sd_onsite_desktop_0?ie=UTF8&pd_rd_plhdr=t&aref=0ahOBXyG8c&th=1

    /**
     * @var ExtendedDetails
     */
    private $extendedDetails;

    const VENDOR = 'ebay';

    public function getData($dom, $xpath, string $productId, int $productDetailsId): ExtendedDetails
    {
        $this->dom = $dom;
        $this->xpath = $xpath;

        try {

            $this->extendedDetails = ExtendedDetails::create([
                'product_details_id' => $productDetailsId,
                'product_id' => $productId,
                'html_description' => $this->getDescription(),
                'brand' => $this->getBrand(),
                'html_features' => $this->getFeatures(),
                'html_product_specfics' => $this->getProductSpecifics(),
                'html_images' => $this->getImages(),

            ]);

            $extendedDetailsVideo = $this->getVideos();
            $this->extendedDetails->setRelation('videos', $extendedDetailsVideo);

            //$this->relatedProductsCarousel();
            //$this->alsoBoughtCarousel();
            $this->extendedDetails->save();
            return $this->extendedDetails;

        } catch (Exception $e) {
            // Manejo de errores aquí (log, notificaciones, etc.)
            throw new Exception('Error al procesar los datos del producto: ' . $e->getMessage());
        }

    }


    public function getDescription(){
        return "";
        $scripts = $this->xpath->query("//script");
        if(!$scripts){
            return [];
        }
        $json = null;
        foreach ($scripts as $script) {
            $scriptContent = $script->textContent;
            if (strpos($scriptContent, '$MC=(window.$MC') !== false) {
                $scriptContent = str_replace(["\r", "\n", '\n', '\r'], '', $scriptContent);
                $startPos = strpos($scriptContent, '$MC=(window.$MC||[]).concat(');
                if ($startPos !== false) {
                    $scriptContent = substr($scriptContent, $startPos + strlen('$MC=(window.$MC||[]).concat('));
                    $scriptContent = trim($scriptContent, ')</script>');
                    $json = json_decode($scriptContent, true);
                    break;
                }
            }
        }

        $p = "";
         if ($json && isset($json["o"]["w"][0][2]["model"]["modules"])) {
             $modules = $json["o"]["w"][0][2]["model"]["modules"];

             foreach($modules as $key => $value){
                 $sections = $value["sections"][0]["dataItems"];
                 foreach($sections as $section){
                     foreach($section["textSpans"] as $textSpan){
                         $p .= "<p>".$textSpan["text"]."</p>";
                     }
                 }
             }
         }

        return ($scriptContent);
    }

    public function getShortDescription(){
        $query = '//meta[@name="twitter:description"]/@content';

        $nodes = $this->xpath->query($query);

        $descriptionHtml = "";

        if(count($nodes)){
            $description = $nodes->item(0)->nodeValue;
            if($description){
                $descriptionHtml = "<p>$description</p>";
            }
        }

        return FixHtml::formatHtml($descriptionHtml);
    }

    public function getBrand(){
        $brand = "";
        return $brand;
    }

    public function getFeatures(){
        $query = '//div[@class="celwidget" and @data-feature-name="productOverview"]';

        $nodes = $this->xpath->query($query);

        $featuresHtml = "";

        if(count($nodes)){
            $features = $nodes->item(0)->nodeValue;
            $featuresHtml = $this->dom->saveHTML($nodes->item(0));
        }

        return FixHtml::formatHtml($featuresHtml);
    }

    public function getProductSpecifics(){
        $query = '//div[@class="vim x-about-this-item"]';

        $nodes = $this->xpath->query($query);

        $productSpecficsHtml = "";

        if(count($nodes)){
            $productSpecficsHtml = $this->dom->saveHTML($nodes->item(0));
        }

        return FixHtml::formatHtml($productSpecficsHtml);
    }



    public function getImages(){
        $query = '//div[@class="aplus-v2 desktop celwidget" and @cel_widget_id="aplus"]';

        $nodes = $this->xpath->query($query);

        $images = [];
        $imagesHtml = "";

        if(count($nodes)){
            $images = $nodes->item(0)->nodeValue;
            $imagesHtml = $this->dom->saveHTML($nodes->item(0));
        }

        return FixHtml::formatHtml($imagesHtml);
    }

    public function getVideos(){
        $chapiAmazonVideosParser = new ChapiEbayVideosParser($this->dom, $this->xpath, $this->extendedDetails->id);
        $extendedDetailsVideo = $chapiAmazonVideosParser->getData();

        return $extendedDetailsVideo;
    }

    public function relatedProductsCarousel()
    {
        $ChapiAmazonExtendedDetailsRelatedProductsParser = new ChapiEbayExtendedDetailsRelatedProductsParser($this->dom, $this->xpath, $this->extendedDetails->id);
        $relatedProducts = $ChapiAmazonExtendedDetailsRelatedProductsParser->getData();

        $this->extendedDetails->setRelation('relatedProducts', $relatedProducts);
    }

    public function alsoBoughtCarousel(){
        $query = '//div[@data-a-carousel-options]//li[@class="a-carousel"]';

        $nodes = $this->xpath->query($query);

        $alsoBoughts = [];

        if(count($nodes)){
            $alsoBoughts = $nodes->item(0)->nodeValue;
        }

        $this->extendedDetails->setRelation('alsoBoughts', $alsoBoughts);

    }


}
