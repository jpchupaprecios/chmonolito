<?php

namespace App\Parsers\Chapi\Interfaces;
use App\Models\ExtendedDetails\ExtendedDetails;
interface ExtendedDetailsParserInterface
{

    public function getData($dom, $xpath, string $productId, int $productDetailsId): ExtendedDetails;

    function getDescription();

    function getBrand();

    function getFeatures();

    function getImages();

    function getVideos();

    function getProductSpecifics();

    function relatedProductsCarousel();

    function alsoBoughtCarousel();
}
