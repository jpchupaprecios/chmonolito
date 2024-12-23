<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Results;

use App\Models\Search\Pagination\Pagination;
use App\Models\Search\Result;
use App\Parsers\Chapi\Walmart\Results\Pagination\ChapiWalmartPaginationParser;
use App\Parsers\Chapi\Walmart\Results\Refinements\ChapiWalmartRefinementsParser;
use Exception;
use DOMDocument;
use DOMXPath;

final class ChapiWalmartResultParser
{
    /**
     * @var mixed
     */
    private $dom;
    /**
     * @var mixed
     */
    private $result;
    /**
     * @var array
     */
    private $products;
    public array $refinements = [];
    public Pagination $pagination;

    public function __construct() {}

    /**
     * @throws Exception
     */
    public function parse($resultData, $vendor, $query, $page): Result
    {
        $this->results = $resultData['dom'];
        $this->products = [];

        $result = new Result();
        $result->setAttribute('vendor', $vendor);
        $result->setAttribute('query', $query);

        $scriptData = $resultData['nextDataScript'];//json_decode($scriptElement ? $scriptElement->textContent : '{}');

        $this->products = ChapiWalmartProductsResultParser::parse($scriptData, $vendor);
        $refinements = ChapiWalmartRefinementsParser::parse($scriptData, $vendor, $query);
        $pagination = ChapiWalmartPaginationParser::parse($scriptData, $page);

        if ($refinements) {
            $this->refinements = $refinements;
        }

        if ($pagination) {
            $this->pagination = $pagination;
        } else {
            $this->pagination = new Pagination();
        }

        $result->setAttribute('totalProducts', count($this->products));
        $result->setRelation('products', $this->products);
        $result->setRelation('pagination', $this->pagination);
        $result->setRelation('refinements', $this->refinements);

        return $result;
    }

}
