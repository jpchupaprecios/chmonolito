<?php

namespace App\Parsers\Chapi\Interfaces;
use App\Models\ExtendedDetails\ExtendedDetails;
interface ExtendedDetailsParserInterface
{

    public function getData($dom, $xpath, string $productId): ExtendedDetails|array;

    function getDescription();

    function getBrand();

    function getFeatures();

    function getImages();

    function getVideos();

    function getProductSpecifics();

    function relatedProductsCarousel();

    function alsoBoughtCarousel();
}
