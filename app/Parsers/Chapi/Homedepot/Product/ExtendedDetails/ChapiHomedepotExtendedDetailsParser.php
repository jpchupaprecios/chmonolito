<?php

namespace App\Parsers\Chapi\Homedepot\Product\ExtendedDetails;
use App\Models\ExtendedDetails\ExtendedDetails;
use App\Models\ExtendedDetails\ExtendedDetailsVideos;
use App\Models\Product\ProductDetails;
use App\Parsers\Chapi\Interfaces\ExtendedDetailsParserInterface;
use App\Helpers\FixHtml;
use DOMDocument;
use DOMXPath;

class ChapiHomedepotExtendedDetailsParser implements ExtendedDetailsParserInterface
{

    //https://www.ebay.com/itm/204981378977?_nkw=Jordan+1+Retro+OG+High+UNC+Toe&itmmeta=01J7DHB5AMMF3B5EVK5W2G3CJM&hash=item2fb9d79fa1:g:bgEAAOSw45Zm3Pvz&amdata=enc%3AAQAJAAABAMxmj%2BiGvOveHXEBClPb29ha20EIkTeezo1kp1STe%2FXkv6w1yBrhDafN7Jo3aUISAU8Lna%2BgbnC4MpFquJgV79j6viwykeAviwEQKCSQWe373LH71Ps5gWXmUVOMvb3jTX5gx5xe%2B6xycXHQAUT%2B29LSpno3fgF2dJZmkCIrXNw9z1aQfNmGya53nfU%2FAuZF5Y30lcaolHH%2BpLUt7HR%2FIEnFkdj%2B0WPCo%2F1stpGuO33vNY306p7Dh%2B%2BF7%2FfU8lIEu6k%2FXF5QeBJg22ToF9bbFTM9Q58QXJVGhXEMbsa6iew1TBk8Cee5WpsBBNZc90HeEUd92SK3WRp1pcEnc7WYl%2FI%3D%7Ctkp%3ABFBMttWssbtk&var=505681542205

    /**
     * @var ExtendedDetails
     */
    private $extendedDetails;

    const VENDOR = 'homedepot';

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
        $chapiAmazonVideosParser = new ChapiHomedepotVideosParser($this->dom, $this->xpath, $this->extendedDetails->id);
        $extendedDetailsVideo = $chapiAmazonVideosParser->getData();

        return $extendedDetailsVideo;
        //$this->extendedDetails->setRelation('videos', $videos);
    }

    public function relatedProductsCarousel()
    {
        $ChapiAmazonExtendedDetailsRelatedProductsParser = new ChapiHomedepotExtendedDetailsRelatedProductsParser($this->dom, $this->xpath, $this->extendedDetails->id);
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
