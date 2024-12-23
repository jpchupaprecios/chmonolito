<?php

namespace App\Parsers\Chapi\Interfaces;
use App\Models\ExtendedDetails\ExtendedDetails;
interface ExtendedDetailsVideosParserInterface
{

    function getData();

    function getVideos();

    function getVideo();

    function getVideoHtml();

    function getTitle();

}
