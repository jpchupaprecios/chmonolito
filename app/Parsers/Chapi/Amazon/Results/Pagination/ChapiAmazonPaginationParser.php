<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Results\Pagination;

use DOMXPath;

final class ChapiAmazonPaginationParser
{
    /**
     * @var Pagination
     */
    private static $pagination;

    public static function parse($xpath, $query): bool|Pagination|\stdClass
	{
		self::$pagination = new \stdClass();

		$selectedElement = $xpath->query('//span[contains(@class, "s-pagination-selected")]')->item(0);
		if (!$selectedElement) {
			return false;
		}

		$current = $selectedElement->textContent;
        self::$pagination->current = trim($current);

		$links = self::iterableElements($xpath, $query);


        self::$pagination->links = $links;

		return self::$pagination;
	}

	private static function iterableElements(DOMXPath $xpath): array
	{
		$links = [];

        $lis = $xpath->query('//a[contains(@class, "s-pagination-item")]');

        foreach ($lis as $li) {
            $classes = $li->getAttribute('class'); // Obtiene el valor del atributo "class"
            $href = str_replace(['///-/es/', '/-/es', ';'], ['', '', '&'], $li->getAttribute('href'));
            if (strpos($classes, 's-pagination-next') !== false) {
                self::setNextLink($href);
            } else {
                $title = $li->textContent;

                $link = new \stdClass();
                $link->link = ($href);
                $link->title = trim($title);

                $links[] = $link;
            }
        }

        $totalPages = count($links);
        if(($title + 0) > $totalPages){
            $totalPages = $title + 0;
        }

        self::$pagination->totalPages = $totalPages;

		return $links;
	}


	protected static function setNextLink($dataUrl): void
	{
		if ($dataUrl && is_string($dataUrl)) {
            self::$pagination->next = ($dataUrl);
		}
	}
}
