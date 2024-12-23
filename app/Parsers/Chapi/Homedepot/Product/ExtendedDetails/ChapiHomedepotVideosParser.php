<?php

namespace App\Parsers\Chapi\Homedepot\Product\ExtendedDetails;
use App\Helpers\FixHtml;
use App\Models\ExtendedDetails\ExtendedDetailsVideo;
use App\Parsers\Chapi\Interfaces\ExtendedDetailsVideosParserInterface;
use App\Models\ExtendedDetails\ExtendedDetailsVideos;
use DOMDocument;
use DOMXPath;

class ChapiHomedepotVideosParser implements ExtendedDetailsVideosParserInterface
{

    public function __construct(DOMDocument $dom, DOMXPath $xpath, $extendedDetailsId)
    {
        $this->dom = $dom;
        $this->xpath = $xpath;
        $this->extendedDetailsId = $extendedDetailsId;
    }

    function getData(){
        $videos = $this->getVideos();

        return $videos;
    }

    function getVideos(){
        $extendedDetailsVideos = ExtendedDetailsVideos::create([
            'extended_details_id' => $this->extendedDetailsId
        ]);

        $query = '//div[@class="a-row a-carousel-controls a-carousel-row a-carousel-has-buttons"]//video';
        $nodes = $this->xpath->query($query);

        $videosArray = [];

        if (count($nodes)) {
            foreach ($nodes as $node) {
                $this->nodeVideo = $node;
                $video = $this->getVideo();

                if($video && $video['title'] && $video['data']){
                    $extendedDetailsVideo = ExtendedDetailsVideo::create([
                        'title' => $video['title'],
                        'data' => $video['data'],
                        'extended_details_videos_id' => $extendedDetailsVideos->id
                    ]);
                }
                /*if ($video) {
                    $videosArray[] = $video;
                }*/
            }
        }

        return $extendedDetailsVideos;

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
