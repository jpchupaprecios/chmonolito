<?php

namespace App\Parsers\Chapi\Amazon\Complete\Product\ExtendedDetails;
use App\Models\ExtendedDetails\ExtendedDetailsRelatedProducts;
use App\Parsers\Chapi\Interfaces\ExtendedDetailsRelatedProductParserInterface;
use DOMDocument;
use DOMXPath;

class ChapiAmazonExtendedDetailsRelatedProductsParser implements ExtendedDetailsRelatedProductParserInterface
{

    public function __construct(DOMDocument $dom, DOMXPath $xpath, $extendedDetailsId)
    {
        $this->dom = $dom;
        $this->xpath = $xpath;
        $this->extendedDetailsId = $extendedDetailsId;
        $this->curl();
    }
    function getData(){
        $relatedProducts = $this->getRelatedProducts();

        return $relatedProducts;
    }

    function getRelatedProducts(){
        $relatedProducts = ExtendedDetailsRelatedProducts::create([
            'extended_details_id' => $this->ExtendedDetailsId
        ]);

        $query = '//div[@class="a-row a-carousel-controls a-carousel-row a-carousel-has-buttons"]//video';
        $nodes = $this->xpath->query($query);

        $relatedProductsArray = [];

        if (count($nodes)) {
            foreach ($nodes as $node) {
                $this->nodeRelatedProduct = $node;
                $relatedProduct = $this->getRelatedProduct();

                if ($relatedProduct) {
                    $relatedProductsArray[] = $relatedProduct;
                }
            }
        }

        foreach ($relatedProductsArray as $relatedProduct) {
            $relatedProducts->relatedProducts()->create($relatedProduct);
        }

        return $relatedProducts->relatedProducts;
    }

    function getRelatedProduct(){
        $relatedProduct = [
            "product_id" => $this->getProductId(),
            "title" => $this->getTitle(),
            "descriptions" => $this->getDescription(),
            "rating" => $this->getScore(),
            "image" => $this->getImage(),
            "price" => $this->getPrice(),
        ];

        return $relatedProduct;
    }

    function getProductId(){
        $parent = $this->nodeRelatedProduct->parentNode->parentNode;

        $query = './/h4';

        $nodes = $this->xpath->query($query, $parent);

        $productId = "";

        if(count($nodes)){
            $productId = $nodes->item(0)->nodeValue;
        }

        return trim($productId);
    }
    function getImage(){
        $parent = $this->nodeRelatedProduct->parentNode->parentNode;

        $query = './/h4';

        $nodes = $this->xpath->query($query, $parent);

        $image = "";

        if(count($nodes)){
            $image = $nodes->item(0)->nodeValue;
        }

        return trim($image);
    }

    function getTitle(){
        $parent = $this->nodeRelatedProduct->parentNode->parentNode;

        $query = './/h4';

        $nodes = $this->xpath->query($query, $parent);

        $title = "";

        if(count($nodes)){
            $title = $nodes->item(0)->nodeValue;
        }

        return trim($title);
    }

    function getPrice(){
        $parent = $this->nodeRelatedProduct->parentNode->parentNode;

        $query = './/h4';

        $nodes = $this->xpath->query($query, $parent);

        $price = 0;

        if(count($nodes)){
            $price = $nodes->item(0)->nodeValue;
        }

        return $price;
    }

    function getRating(){
        $parent = $this->nodeRelatedProduct->parentNode->parentNode;

        $query = './/h4';

        $nodes = $this->xpath->query($query, $parent);

        $rating = "";

        if(count($nodes)){
            $rating = $nodes->item(0)->nodeValue;
        }

        return trim($rating);
    }

    function getDescription(){
        $parent = $this->nodeRelatedProduct->parentNode->parentNode;

        $query = './/h4';

        $nodes = $this->xpath->query($query, $parent);

        $description = "";

        if(count($nodes)){
            $description = $nodes->item(0)->nodeValue;
        }

        return trim($description);
    }


    private function curl(){
        //base

        //https://www.amazon.com/sspa/paginate?ASIN=B09TRCDYGP&wName=sp_detail
        //&count=5
        //&offset=15
        //&pg=3
        //&tot=400
        //&num=5
        //&cc=15
        //&start=15

        //1
        //https://www.amazon.com/sspa/paginate
        //?cc=10
        //&widgetLocale=es_US
        //&isBlended=false
        //&showHelpfulSentence=false
        //&start=10
        //&ASIN=B09TRCDYGP
        //&isPrimeMember=false
        //&storeId=wireless
        //&isGetBlendedWidgetsAPI=false
        //&wName=sp_detail
        //&widgetGroup=desktop-dp-sims
        //&isMultiPlacementRequest=true
        //&doNotShowProductAttributes=false
        //&isPoweredByJavelin=false
        //&isPantry=0
        //&isFresh=0
        //&count=5
        //&offset=10
        //&pg=2
        //&tot=400
        //&num=5


        //2
        //https://www.amazon.com/sspa/paginate
        //?cc=0
        //&widgetLocale=es_US
        //&isBlended=false
        //&showHelpfulSentence=false
        //&start=10
        //&ASIN=B09TRCDYGP
        //&isPrimeMember=false
        //&storeId=wireless
        //&isGetBlendedWidgetsAPI=false
        //&wName=sp_detail
        //&widgetGroup=desktop-dp-sims
        //&isMultiPlacementRequest=true
        //&doNotShowProductAttributes=false
        //&isPoweredByJavelin=false
        //&isPantry=0
        //&isFresh=0
        //&count=2
        //&offset=10
        //&pg=1
        //&tot=245
        //&num=2

        //3
        //https://www.amazon.com/sspa/paginate?cc=10
        //&widgetLocale=es_US
        //&themeSelector=cfv
        //&isBlended=false
        //&showHelpfulSentence=false
        //&start=10
        //&ASIN=B0CZ3NSNSS
        //&isPrimeMember=false
        //&storeId=wireless
        //&isGetBlendedWidgetsAPI=false
        //&wName=sp_detail_thematic
        //&widgetGroup=desktop-dp-sims
        //&isMultiPlacementRequest=true
        //&doNotShowProductAttributes=false
        //&strategyId=SPThematicCFV
        //&isPoweredByJavelin=false
        //&isPantry=0
        //&isFresh=0
        //&count=5
        //&offset=10
        //&pg=2
        //&tot=79
        //&num=5
    }

}
