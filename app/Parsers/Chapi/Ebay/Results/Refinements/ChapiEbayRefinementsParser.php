<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Results\Refinements;

use App\Helpers\NotAllowed;
use App\Models\Search\Refinements\Refinement;
use App\Models\Search\Refinements\RefinementLinks;
use DOMXPath;

final class ChapiEbayRefinementsParser
{
    public static function parse($xpath, $vendor, $query): array|bool
    {
        $notAllowed = new NotAllowed($vendor);

        $filters = $xpath->query('//div[contains(@class, "srp-rail__left")]//ul[contains(@class, "x-refine__left__nav")]//li');

        if ($filters->length === 0) {
            return false;
        }

        $refinements = [];
        foreach ($filters as $item) {
            $title = '';
            $titleElement = $xpath->query('.//h3[contains(@class, "x-refine__item")]', $item)->item(0);
            if ($titleElement) {
                $title = $titleElement->textContent;
            }

            $titleElement = $xpath->query('.//h3[contains(@class, "x-refine__item__title")]', $item)->item(0);
            if ($titleElement) {
                $title = $titleElement->textContent;
            }

            if ($title) {
                $refinement = new Refinement();
                $refinement->setAttribute('title', $title);
                $links = [];

                $filtersOptions = $xpath->query('.//ul[contains(@class, "x-refine__group")]//li', $item);

                foreach ($filtersOptions as $itemOption) {
                    $title = self::getText($itemOption, './/span[contains(@class, "cbx x-refine__multi-select-cbx")]', $xpath);
                    $articles = self::getText($itemOption, './/span[contains(@class, "cbx x-refine__multi-select-cbx")]//span', $xpath);
                    $title = str_replace($articles, '', $title);

                    $linkElement = $xpath->query('.//a[contains(@class, "x-refine__multi-select")]', $itemOption)->item(0);
                    if ($linkElement) {
                        $checked = $xpath->query('.//input[contains(@class, "checkbox__control") and @type="checkbox" and following-sibling::span[contains(@class, "checkbox__icon")]//svg[contains(@class, "checkbox__checked")]]', $itemOption)->length > 0;

                        if ($title) {
                            $title = trim($title);
                            $link = new RefinementLinks();
                            $link->setAttribute('title', $title);
                            $link->setAttribute('link', $linkElement->getAttribute('href'));
                            $link->setAttribute('checked', $checked);

                            $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($title);

                            if($isAllowedByKeyword){
                                $links[] = $link;
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

    private static function getText($item, $selector, $xpath): string
    {
        $text = '';
        $element = $xpath->query($selector, $item)->item(0);
        if ($element) {
            $text = $element->textContent;
        }
        return $text;
    }
}
