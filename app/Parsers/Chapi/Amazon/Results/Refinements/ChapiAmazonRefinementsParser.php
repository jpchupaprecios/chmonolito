<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Results\Refinements;


use App\Helpers\NotAllowed;
use App\Models\Search\Refinements\Refinement;
use App\Models\Search\Refinements\RefinementLinks;

use App\Services\Chapi\Amazon\ChapiAmazonUrlEncoderService;

final class ChapiAmazonRefinementsParser
{
    public static function parse($xpath, $vendor, $query): array|bool
    {

        $notAllowed = new NotAllowed($vendor);

        $filters = $xpath->query('//div[@id="s-refinements"]//div[@id="filters"]')->item(0);
        if (!$filters) {
            return false;
        }

        $refinements = [];

        foreach ($xpath->query('//div[@id="filters"]//div') as $div) {
            $titleElement = $xpath->query('.//span', $div)->item(0);
            if ($titleElement) {
                $title = $titleElement->textContent;
                if ($title === 'Color') {
                    $links = [];
                    foreach ($xpath->query('.//li', $div) as $li) {
                        $nameElement = $xpath->query('.//a[contains(@class, "s-navigation-item")]', $li)->item(0);
                        if ($nameElement) {
                            $name = $nameElement->getAttribute('title');
                            $link = $nameElement->getAttribute('href');
                            $checked = !$xpath->query('.//a//span[contains(@class, "a-declarative")]', $li)->length;
                            $linkObj = new \stdClass();
                            $t = trim(utf8_decode($name));
                            $linkObj->title = mb_convert_encoding($t, 'UTF-8', 'ISO-8859-1');
                            $linkObj->link = $link;
                            $linkObj->checked = $checked;

                            $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($t);

                            if($isAllowedByKeyword){
                                $links[] = $linkObj;
                            }
                        }
                    }
                    if (!empty($links)) {
                        $refinement = new \stdClass();
                        $refinement->title = $title;
                        $refinement->links = $links;
                        $refinements[] = $refinement;
                    }
                } elseif ($title !== 'Availability' && $title !== 'Condition') {
                    $links = [];
                    foreach ($xpath->query('.//ul/li', $div->nextSibling) as $li) {
                        $childs = $xpath->query('.//li', $li);
                        if ($childs->length > 0) {
                            foreach ($childs as $child) {
                                $nameElement = $xpath->query('.//span', $child)->item(0);
                                $linkElement = $xpath->query('.//a', $child)->item(0);
                                if ($nameElement && $linkElement) {
                                    $name = $nameElement->textContent;
                                    $link = $linkElement->getAttribute('href');
                                    $checked = $xpath->query('.//span[contains(@class, "a-text-bold")]', $linkElement)->length > 0;
                                    if (!$xpath->query('.//span[contains(@class, "a-color-base")]', $linkElement)->length) {
                                        $checked = true;
                                    }
                                    $linkObj = new \stdClass();
                                    $t = trim(utf8_decode($name));
                                    $linkObj->title = mb_convert_encoding($t, 'UTF-8', 'ISO-8859-1');
                                    $linkObj->link = $link;
                                    $linkObj->checked = $checked;

                                    $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($t);

                                    if($isAllowedByKeyword){
                                        $links[] = $linkObj;
                                    }
                                }
                            }
                        } else {
                            $nameElement = $xpath->query('.//span', $li)->item(0);
                            $linkElement = $xpath->query('.//a', $li)->item(0);
                            if ($nameElement && $linkElement) {
                                $name = $nameElement->textContent;
                                $link = $linkElement->getAttribute('href');
                                $checked = $xpath->query('.//span[contains(@class, "a-text-bold")]', $linkElement)->length > 0;
                                $linkObj = new \stdClass();
                                $t = trim(utf8_decode($name));
                                $linkObj->title = mb_convert_encoding($t, 'UTF-8', 'ISO-8859-1');
                                $linkObj->link = $link;
                                $linkObj->checked = $checked;

                                $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($t);

                                if($isAllowedByKeyword){
                                    $links[] = $linkObj;
                                }
                            }
                        }
                    }
                    if (!empty($links)) {
                        $refinement = new \stdClass();
                        $refinement->title = mb_convert_encoding($title, 'UTF-8', 'ISO-8859-1');
                        $refinement->links =  $links;
                        $refinements[] = $refinement;
                    }
                }
            }
        }

        $filtersElement = $xpath->query('//div[@id="s-refinements"]//div[@id="filters"]')->item(0);
        if ($filtersElement) {
            $filtersElement->parentNode->removeChild($filtersElement);
        }

        $priceRefinementsElement = $xpath->query('//div[@id="s-refinements"]//div[@id="priceRefinements"]')->item(0);
        if ($priceRefinementsElement) {
            $priceRefinementsElement->parentNode->removeChild($priceRefinementsElement);
        }

        $departmentsElement = $xpath->query('//div[@id="s-refinements"]//div[@id="departments"]')->item(0);
        if ($departmentsElement) {
            $titleElement = $xpath->query(
                './/span[contains(@class, "a-size-base a-color-base puis-bold-weight-text")]',
                $departmentsElement
            )->item(0);
            if ($titleElement) {
                $title = $titleElement->textContent;
                if ($title !== 'Delivery' && $title !== 'Delivery Day') {
                    $links = [];
                    foreach ($xpath->query('.//li/span/a', $departmentsElement) as $a) {
                        $nameElement = $xpath->query('.//span[contains(@class, "a-size-base a-color-base")]', $a)->item(0);
                        if ($nameElement) {
                            $name = $nameElement->textContent;
                            $link = $a->getAttribute('href');
                            $checked = $xpath->query('.//span[contains(@class, "a-text-bold")]', $a)->length > 0;
                            $linkObj = new \stdClass();
                            $t = trim(utf8_decode($name));
                            $linkObj->title = mb_convert_encoding($t, 'UTF-8', 'ISO-8859-1');
                            $linkObj->link = $link;
                            $linkObj->checked = $checked;

                            $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($t);

                            if($isAllowedByKeyword){
                                $links[] = $linkObj;
                            }
                        }
                    }
                    if (!empty($links)) {
                        $refinement = new \stdClass();
                        $refinement->title = mb_convert_encoding($title, 'UTF-8', 'ISO-8859-1');
                        $refinement->links =  $links;
                        $refinements[] = $refinement;
                    }
                }
            }
        }

        $reviewsElement = $xpath->query('//div[@id="s-refinements"]//div[@id="reviewsRefinements"]')->item(0);
        if (!$reviewsElement) {
            return [];
        }
        $titleElement = $xpath->query(
            './/span[contains(@class, "a-size-base a-color-base puis-bold-weight-text")]',
            $reviewsElement
        )->item(0);
        if ($titleElement) {
            $title = $titleElement->textContent;
            if ($title !== 'Delivery' && $title !== 'Delivery Day') {
                $links = [];
                foreach ($xpath->query('.//li/span/a', $reviewsElement) as $a) {
                    $nameElement = $xpath->query('.//span[contains(@class, "a-icon-alt")]', $a)->item(0);
                    if ($nameElement) {
                        $name = trim($nameElement->textContent);
                        $link = $a->getAttribute('href');
                        $checked = $xpath->query('.//span[contains(@class, "a-text-bold")]', $a)->length > 0;
                        $linkObj = new \stdClass();
                        $t = trim(utf8_decode($name));
                        $linkObj->title = mb_convert_encoding($t, 'UTF-8', 'ISO-8859-1');
                        $linkObj->link = $link;
                        $linkObj->checked = $checked;

                        $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($t);

                        if($isAllowedByKeyword){
                            $links[] = $linkObj;
                        }
                    }
                }
                if (!empty($links)) {
                    $refinement = new \stdClass();
                    $refinement->title = trim(mb_convert_encoding($title, 'UTF-8', 'ISO-8859-1'));
                    $refinement->links = $links;
                    $refinements[] = $refinement;
                }
            }
        }

        $brandsElement = $xpath->query('//div[@id="s-refinements"]//div[@id="brandsRefinements"]')->item(0);
        if ($brandsElement) {
            $titleElement = $xpath->query(
                './/span[contains(@class, "a-size-base a-color-base puis-bold-weight-text")]',
                $brandsElement
            )->item(0);
            if ($titleElement) {
                $title = $titleElement->textContent;
                if ($title !== 'Delivery' && $title !== 'Delivery Day') {
                    $links = [];
                    foreach ($xpath->query('.//li/span/a', $brandsElement) as $a) {
                        $nameElement = $xpath->query('.//span[contains(@class, "a-size-base a-color-base")]', $a)->item(0);
                        if ($nameElement) {
                            $name = $nameElement->textContent;
                            $link = $a->getAttribute('href');
                            $checked = $xpath->query('.//span[contains(@class, "a-text-bold")]', $a)->length > 0;
                            $linkObj = new \stdClass();
                            $t = trim(utf8_decode($name));
                            $linkObj->title = mb_convert_encoding($t, 'UTF-8', 'ISO-8859-1');
                            $linkObj->link = $link;
                            $linkObj->checked = $checked;

                            $isAllowedByKeyword = $notAllowed->isAllowedByKeyword($t);

                            if($isAllowedByKeyword){
                                $links[] = $linkObj;
                            }
                        }
                    }
                    if (!empty($links)) {
                        $refinement = new \stdClass();
                        $refinement->title = trim(mb_convert_encoding($title, 'UTF-8', 'ISO-8859-1'));
                        $refinement->links = $links;
                        $refinements[] = $refinement;
                    }
                }
            }
        }

        return $refinements;
    }
}
