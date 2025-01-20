<?php

namespace App\Parsers\Chapi\Amazon\Complete\Product\ExtendedDetails;
use App\Helpers\FixHtml;
use App\Parsers\Chapi\Interfaces\ExtendedDetailsVideosParserInterface;
use App\Models\ExtendedDetails\ExtendedDetailsVideos;
use DOMDocument;
use DOMXPath;
use App\Models\ExtendedDetails\ExtendedDetailsVideo;
class ChapiAmazonVideosParser implements ExtendedDetailsVideosParserInterface
{

    public function __construct(DOMDocument $dom, DOMXPath $xpath, $extendedDetailsId)
    {
        $this->dom = $dom;
        $this->xpath = $xpath;
        $this->extendedDetailsId = $extendedDetailsId;
    }

    function getData(){
        $extendedDetailsVideo = $this->getVideos();

        return $extendedDetailsVideo;
    }

    function getVideos(){
        $extendedDetailsVideos = ExtendedDetailsVideos::create([
            'extended_details_id' => $this->extendedDetailsId
        ]);

        //$extendedDetailsVideos = new ExtendedDetailsVideos();
        //$extendedDetailsVideos->extended_details_id = $this->extendedDetailsId;
        /*// Crea la instancia de ExtendedDetailsVideos y guárdala en la base de datos
        $extendedDetailsVideos = ExtendedDetailsVideos::create([
            'extended_details_id' => $this->productId
        ]);*/

        $query = '//div[@class="a-row a-carousel-controls a-carousel-row a-carousel-has-buttons"]//video';
        $nodes = $this->xpath->query($query);

        $videosArray = [];

        if (count($nodes)) {
            foreach ($nodes as $node) {
                $this->nodeVideo = $node;
                $video = $this->getVideo();
                if($video && $video['title'] && $video['data']){
                    $extendedDetailsVideo = new ExtendedDetailsVideo();
                    $extendedDetailsVideo->title = $video['title'];
                    $extendedDetailsVideo->data = $video['data'];
                    $extendedDetailsVideo->extended_details_videos_id = $extendedDetailsVideos->id;

                    $videosArray[] = $extendedDetailsVideo;
                }
                /*if ($video) {
                    $videosArray[] = $video;
                }*/
            }
        }

        $extendedDetailsVideos->videos()->saveMany($videosArray);

        return $videosArray;

        /*foreach ($videosArray as $videoData) {
            $extendedDetailsVideos->videos()->create($videoData);
        }

        // Cargar los videos relacionados y devolver solo el array de videos
        return $extendedDetailsVideos->videos;*/
    }

    function getVideo(){
        $extendedDetailsVideo = [
            "title" => "",
            "data" => ""
        ];

       $title = $this->getTitle();

        if($title){
            $extendedDetailsVideo['title'] = trim($title);
        }

        $videoHtml = $this->getVideoHtml();

        if($videoHtml){
            $extendedDetailsVideo['data'] = FixHtml::formatHtml($videoHtml);
        }

        return $extendedDetailsVideo;
    }

    function getVideoHtml(){
        $videoHtml = $this->dom->saveHTML($this->nodeVideo);
        return $videoHtml;
    }

    function getTitle(){
        $parent = $this->nodeVideo->parentNode->parentNode;

        $query = './/h4';

        $nodes = $this->xpath->query($query, $parent);

        $title = "";

        if(count($nodes)){
            $title = $nodes->item(0)->nodeValue;
        }

        return $title;
    }

}
