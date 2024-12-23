<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Results\Pagination;

use App\Models\Search\Pagination\Pagination;
use App\Models\Search\Pagination\PaginationLink;
use App\Models\Search\Pagination\PaginationNext;
use DOMXPath;

final class ChapiEbayPaginationParser
{
    public static function parse($xpath, $currentPage): Pagination
    {
        $pagination = new Pagination();
        $links = [];
        $pagination->setAttribute('current', $currentPage);

        $paginationItems = $xpath->query('.//ul[contains(@class, "pagination__items")]//li');

        if ($paginationItems->length > 0) {
            foreach ($paginationItems as $paginationLi) {
                $link = new PaginationLink();

                $a = $xpath->query('.//a', $paginationLi)->item(0);
                if ($a) {
                    $linkData = $a->getAttribute('href');
                    $title = $a->textContent;
                    $link->setAttribute('link', ($linkData));
                    $link->setAttribute('title', trim($title));
                }

                $links[] = $link;
            }
        }

        $pagination->setAttribute('totalPages', count($links));
        $pagination->setRelation('links', $links);

        return $pagination;
    }
}
