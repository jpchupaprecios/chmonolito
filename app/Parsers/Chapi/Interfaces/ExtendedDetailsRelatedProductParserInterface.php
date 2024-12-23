<?php

namespace App\Parsers\Chapi\Interfaces;
interface ExtendedDetailsRelatedProductParserInterface
{

    function getData();

    function getRelatedProducts();

    function getRelatedProduct();

    function getProductId();

    function getImage();

    function getTitle();

    function getPrice();

    function getRating();

    function getDescription();
}
