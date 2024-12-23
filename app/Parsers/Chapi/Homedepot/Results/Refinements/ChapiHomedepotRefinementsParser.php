<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Homedepot\Results\Refinements;

use App\Helpers\NotAllowed;
use App\Models\Search\Refinements\Refinement;
use App\Models\Search\Refinements\RefinementLinks;
use DOMXPath;

final class ChapiHomedepotRefinementsParser
{
    public static function parse($xpath, $vendor, $query): array|bool
    {

        $notAllowed = new NotAllowed($vendor);

        $filters = $xpath->query('//div[contains(@class, "results-dimensions")]')->item(0);

        if (!$filters) {
            return false;
        }

        $refinements = [];
        foreach ($filters->childNodes as $item) {
            if ($item->nodeType !== XML_ELEMENT_NODE) {
                continue;
            }

            $title = '';
            $h2 = $xpath->query('.//h2', $item)->item(0);
            if ($h2) {
                $title = $h2->textContent;
            }

            if ($title) {
                $refinement = new Refinement();
                $refinement->setAttribute('title', $title);
                $links = [];

                $filtersOptions = $xpath->query('.//div[contains(@class, "grid")]//div[contains(@class, "dimension__item")]', $item);

                foreach ($filtersOptions as $itemOption) {
                    $label = $xpath->query('.//label/a/h3', $itemOption)->item(0);
                    $link = $xpath->query('.//label/a[contains(@class, "refinement__link")]', $itemOption)->item(0);

                    if($link){
                        $link = $link->getAttribute('href');
                    }

                    if (!$label || !$link) {
                        continue;
                    }
                    $title = $label->textContent;

                    $checked = false;

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

                if (!empty($links)) {
                    $refinement->setRelation('links', $links);
                    $refinements[] = $refinement;
                }
            }
        }

        return $refinements;
    }
}
