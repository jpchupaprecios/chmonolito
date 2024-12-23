<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Results\Pagination;

use App\Models\Search\Pagination\Pagination;

final class ChapiWalmartPaginationParser
{
	public static function parse($scriptData, $currentPage): Pagination
	{
        $paginationData = null;
        if($scriptData){
            $paginationData = $scriptData->props->pageProps->initialData->searchResult->paginationV2;
        }

		$pagination = new Pagination();
		$links = [];
		$pagination->setAttribute('current', $currentPage);

        if($paginationData){
            $pagination->setAttribute('totalPages', $paginationData->maxPage);
        }else{
            $pagination->setAttribute('totalPages', 0);
        }

		$pagination->setRelation('links', $links);

		return $pagination;
	}
}
