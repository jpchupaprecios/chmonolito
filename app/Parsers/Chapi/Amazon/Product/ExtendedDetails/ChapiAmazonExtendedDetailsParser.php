<?php

namespace App\Parsers\Chapi\Amazon\Product\ExtendedDetails;
use App\Models\ExtendedDetails\ExtendedDetails;
use App\Models\Product\ProductDetails;
use App\Parsers\Chapi\Interfaces\ExtendedDetailsParserInterface;
use App\Helpers\FixHtml;
use DOMDocument;
use DOMXPath;

class ChapiAmazonExtendedDetailsParser implements ExtendedDetailsParserInterface
{

    //https://www.amazon.com/dp/B0CZ3NSNSS/ref=syn_sd_onsite_desktop_0?ie=UTF8&pd_rd_plhdr=t&aref=0ahOBXyG8c&th=1

    /**
     * @var ExtendedDetails
     */
    private $extendedDetails;

    const VENDOR = 'amazon';

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
        $query = '//div[@class="celwidget" and @data-feature-name="featurebullets"]';

        $nodes = $this->xpath->query($query);

        $descriptionHtml = "";

        if(count($nodes)){
            $description = $nodes->item(0)->nodeValue;
            if($description){
                $descriptionHtml = $this->dom->saveHTML($nodes->item(0));
            }
        }

        return FixHtml::formatHtml($descriptionHtml);
    }

    public function getBrand(){
        $query = '//div[@class="celwidget" and @data-feature-name="productOverview"]';

        $nodes = $this->xpath->query($query);

        $brand = "";

        if(count($nodes)){
            $query = '//table//tr';
            $trs = $this->xpath->query($query, $nodes->item(0));

            foreach($trs as $tr){
                $tds = $tr->getElementsByTagName('td');
                if($tds->length > 1){
                    $td = $tds->item(0);
                    $nodeValue = strtolower(trim($td->nodeValue));
                    if(strpos($nodeValue, "marca") !== false || strpos($nodeValue, "brand") !== false){
                        $brand = $tds->item(1)->nodeValue;
                        break;
                    }
                }
            }
        }

        if($brand){
            $brand = trim($brand);
        }

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
        $query = '//div[@id="prodDetails"]';

        $nodes = $this->xpath->query($query);

        $productSpecficsHtml = "";

        if(count($nodes)){
            $query = '//div[@id="prodDetails"]//h2';

            $titleNode = $this->xpath->query($query);
            $title = "";

            if($titleNode->item(0)){
                $title = $titleNode->item(0)->nodeValue;
            }

            if($title){
                $title = FixHtml::formatHtml(trim($title));
                if($title == "Product information"){
                    $title = "Información del producto";
                }
            }

            $specificationsQuery = '//div[@id="prodDetails"]//div[@class="a-row a-spacing-base"]';

            $specifications = $this->xpath->query($specificationsQuery);

            $productSpecficsHtml = $this->dom->saveHTML($specifications->item(0));

            if($title && $productSpecficsHtml){
                $productSpecficsHtml = "<h2>$title</h2>" . $productSpecficsHtml;
            }
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

        $images = FixHtml::formatHtml($imagesHtml);

        //remplazo "data-src" por "src"
        //$images = str_replace('data-src', 'src', $images);
        $modified_string = preg_replace_callback(
            '/<img\s+[^>]*>/i', // Expresión regular para encontrar etiquetas <img>
            function ($matches) {
                $img_tag = $matches[0];
                // Verificamos si existe el atributo data-src
                if (preg_match('/data-src="([^"]+)"/i', $img_tag, $data_src_match)) {
                    $data_src = $data_src_match[1];
                    // Reemplazamos o agregamos el atributo src con el valor de data-src
                    if (preg_match('/src="([^"]*)"/i', $img_tag)) {
                        // Reemplazamos el valor de src
                        $img_tag = preg_replace('/src="([^"]*)"/i', 'src="' . $data_src . '"', $img_tag);
                    } else {
                        // Agregamos el atributo src si no existe
                        $img_tag = preg_replace('/<img/i', '<img src="' . $data_src . '"', $img_tag);
                    }
                    // Eliminamos el atributo data-src
                    $img_tag = preg_replace('/\s+data-src="[^"]*"/i', '', $img_tag);
                }
                return $img_tag;
            },
            $images
        );

        return $modified_string;
    }

    public function getVideos(){
        $chapiAmazonVideosParser = new ChapiAmazonVideosParser($this->dom, $this->xpath, $this->extendedDetails->id);
        $extendedDetailsVideo = $chapiAmazonVideosParser->getData();

        return $extendedDetailsVideo;
        //$this->extendedDetails->setRelation('videos', $videos);
    }

    public function relatedProductsCarousel()
    {
        $ChapiAmazonExtendedDetailsRelatedProductsParser = new ChapiAmazonExtendedDetailsRelatedProductsParser($this->dom, $this->xpath, $this->extendedDetails->id);
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
