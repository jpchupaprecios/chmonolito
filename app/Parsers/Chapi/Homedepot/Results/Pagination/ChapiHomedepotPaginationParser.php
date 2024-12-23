<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Homedepot\Results\Pagination;

use App\Models\Search\Pagination\Pagination;
final class ChapiHomedepotPaginationParser
{
    public static function parse($paginationData, $currentPage): Pagination
    {
        $pagination = new Pagination();
        $links = [];

        $pagination->setAttribute('current', $currentPage);
        $pagination->setAttribute('totalPages', 1);
        $pagination->setRelation('links', $links);

        return $pagination;
    }
}
