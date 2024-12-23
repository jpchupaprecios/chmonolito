<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Results\Refinements;

use App\Helpers\NotAllowed;
use App\Models\Search\Refinements\Refinement;
use App\Models\Search\Refinements\RefinementLinks;
use App\Services\Chapi\Homedepot\ChapiHomedepotUrlEncoderService;

final class ChapiWalmartRefinementsParser
{
	public static function parse($scriptData, $vendor, $query): array|bool
	{
        $notAllowed = new NotAllowed($vendor);

		$refinements = [];
		if (isset($scriptData->props->pageProps->initialData->searchResult->modules) && $scriptData->props->pageProps->initialData->searchResult->modules) {
			$refinements = self::itera(
				$scriptData->props->pageProps->initialData->searchResult->modules->topNavFacets,
				$refinements,
				$vendor,
				$query,
                $notAllowed
			);
			$refinements = self::itera(
				$scriptData->props->pageProps->initialData->searchResult->modules->allSortAndFilterFacets,
				$refinements,
				$vendor,
				$query,
                $notAllowed
			);
		}

		return $refinements;
	}

	private static function itera($filters, &$refinements, $vendor, $query, $notAllowed)
	{
		foreach ($filters as $item) {
			$title = $item->name;
			$type = 'default';
			if (strtolower($title) === 'precio' || strtolower($title) === 'price') {
				$type = 'range';
			}

			if ($title) {
				$refinement = new Refinement();
				$refinement->setAttribute('title', $title);
				$links = [];

				if ($item->values) {
					$filtersOptions = $item->values;

					foreach ($filtersOptions as $itemOption) {
						$title = $itemOption->name;

						$linkUrl = '';
						$checked = (bool) $itemOption->isSelected;

						if ($title) {
							$title = trim($title);
                            $linkObj = new RefinementLinks();
                            $linkObj->setAttribute('title', $title);
                            $linkObj->setAttribute('link', '');
                            $linkObj->setAttribute('checked', $checked);

                            $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);

                            if($isAllowedByKeyword){
                                $links[] = $linkObj;
                            }
						}
					}
				}

				if (!empty($links)) {
					$refinement->setRelation('links', $links);
					$refinements[] = $refinement;
				}
			}
		}
		return $refinements;
	}

}
