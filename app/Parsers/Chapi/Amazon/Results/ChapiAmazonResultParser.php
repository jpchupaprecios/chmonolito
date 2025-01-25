<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Results;

use App\Parsers\Chapi\Amazon\Results\Pagination\ChapiAmazonPaginationParser;
use App\Parsers\Chapi\Amazon\Results\Refinements\ChapiAmazonRefinementsParser;

use DOMDocument;
use DOMXPath;

final class ChapiAmazonResultParser
{
    public array $refinements = [];
    public $pagination;

    public function parse($result, $vendor, $query, $debug = false): array|Result
    {
        if(!$result){
            return [];
        }

        if ($debug && isset($result['debugging'])) {
            //$debugging = $result['debugging'];
            //$debugging['methods']['parse']['benchmark']['start_global_parse'] = microtime(true);
        }
        $this->results = $this->loadHtml($result);
        $this->products = [];
        $paginationHtml = '';
        $refinementsHtml = '';


        $resultObj = new \stdClass();
        $resultObj->vendor = $vendor;
        $resultObj->query = $query;


        $resultsProducts = $this->results->query('//div[@data-asin and string-length(@data-asin) > 0]');
        $refinementsHtmlNodeList = $this->results->query('//div[@id="s-refinements"]');
        $paginationHtmlNodeList = $this->results->query("//div[contains(@class, 's-pagination-container')]");

        $refinementsHtml = "";
        if ($refinementsHtmlNodeList->length > 0) {
            $dom = new DOMDocument();
            $dom->appendChild($dom->importNode($refinementsHtmlNodeList->item(0), true));
            $refinementsHtml = $dom->saveHTML();
        }

        $paginationHtml = "";
        if ($paginationHtmlNodeList->length > 0) {
            $dom = new DOMDocument();
            $dom->appendChild($dom->importNode($paginationHtmlNodeList->item(0), true));
            $paginationHtml = $dom->saveHTML();
        }

        foreach ($resultsProducts as $resultsProduct) {
            $this->products[] = $resultsProduct;
        }

        if ($paginationHtml) {
            $this->pagination = ChapiAmazonPaginationParser::parse($this->loadHtml($paginationHtml), $query);
            //$debugging['methods']['parse']['benchmark']['end_pagination_parse'] = microtime(true);
            //$debugging['methods']['parse']['benchmark']['total_pagination_parse'] = $debugging['methods']['parse']['benchmark']['end_pagination_parse'] - $debugging['methods']['parse']['benchmark']['start_pagination_parse'];
        } else {
            $this->pagination = new \stdClass();
        }

        if ($refinementsHtml) {
            //$debugging['methods']['parse']['benchmark']['start_refinements_parse'] = microtime(true);
            $refinements = ChapiAmazonRefinementsParser::parse($this->loadHtml($refinementsHtml), $vendor, $query);
            if ($refinements) {
                $this->refinements = $refinements;
            }
            //$debugging['methods']['parse']['benchmark']['end_refinements_parse'] = microtime(true);
            //$debugging['methods']['parse']['benchmark']['total_refinements_parse'] = $debugging['methods']['parse']['benchmark']['end_refinements_parse'] - $debugging['methods']['parse']['benchmark']['start_refinements_parse'];
        }

        //$debugging['methods']['parse']['benchmark']['start_products_parse'] = microtime(true);
        $this->products = ChapiAmazonProductsResultParser::parse($this->products, $vendor);
        //$debugging['methods']['parse']['benchmark']['end_products_parse'] = microtime(true);
        //$debugging['methods']['parse']['benchmark']['total_products_parse'] = $debugging['methods']['parse']['benchmark']['end_products_parse'] - $debugging['methods']['parse']['benchmark']['start_products_parse'];

        $resultObj->totalProducts = count($this->products);
        $resultObj->products = $this->products;
        $resultObj->pagination = $this->pagination;
        $resultObj->refinements = $this->refinements;

        $ret = [];
        //if ($debug) {
        //	$debugging['methods']['parse']['benchmark']['end_global_parse'] = microtime(true);
        //	$debugging['methods']['parse']['benchmark']['total_global_parse'] = $debugging['methods']['parse']['benchmark']['end_global_parse'] - $debugging['methods']['parse']['benchmark']['start_global_parse'];
        //	$ret['debugging'] = $debugging;
        //}

        $ret['result'] = $resultObj;

        return $ret;
    }

    protected function loadHtml(string $html): DOMXPath
    {
        $html = str_replace("\n", '', $html);
        $dom = new DOMDocument();
        @$dom->loadHTML($html);
        return new DOMXPath($dom);
    }

    protected function getTranslateRefinements(): array
    {
        return [];
    }

    protected function translateRefinements($translatedTexts): void {}
}
