<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Results\Refinements;

use App\Models\Search\Refinements\Refinement;
use App\Models\Search\Refinements\RefinementLinks;
use stdClass;

final class ChapiWalmartRefinementParser
{
	public static function parse($dom)
	{
		$filters = $dom->find('#s-refinements #filters', 0);
		if (!$filters) {
			return false;
		}

		$priceRefinementsUl = $dom->find('#priceRefinements > ul', 0);
		$priceRefinements = [];

		if ($priceRefinementsUl) {
			foreach ($priceRefinementsUl->find('li') as $priceRefinementsLi) {
				if ($priceRefinementsLi->find('.a-size-base.a-color-base', 0) && $priceRefinementsLi->find(
					'a.a-link-normal.s-navigation-item',
					0
				)) {
					$name = $priceRefinementsLi->find('.a-size-base.a-color-base', 0)->plaintext;
					$link = $priceRefinementsLi->find('a.a-link-normal.s-navigation-item', 0)->href;
					$checked = (bool) $priceRefinementsLi->find('a.a-link-normal.s-navigation-item', 0)->find('.a-text-bold', 0);

					if ($name && $link) {
						$name = $name;
						$priceRefinements[] = [
							'name' => $name,
							'link' => $link,
							'checked' => $checked,
						];
					}
				}
			}
			$priceRefinementsUl->outertext = '';
		}

		$otherRefinements = [];
		foreach ($dom->find('#filters > div') as $div) {
			$title = $div->find('span', 0)->plaintext;
			$links = [];


			if ($title !== 'Availability' && $title !== 'Condition') {
				foreach ($div->next_sibling('ul')->find('li') as $li) {
					$name = $li->find('span', 0)->plaintext;
					$link = $li->find('a', 0)->href;
					$checked = (bool) $li->find('a', 0)->find('.a-text-bold', 0);

					if (!$li->find('a', 0)->find('.a-color-base', 0)) {
						$checked = true;
					}

					$links[] = [
						'name' => trim($name),
						'link' => $link,
						'checked' => $checked,
					];
				}

				$otherRefinements[] = [
					'title' => $title,
					'links' => $links,
				];
			}
		}

		$refinementsBase = [];
		$dom->find('#s-refinements #filters', 0)->remove();

		if ($dom->find('#s-refinements #priceRefinements')) {
			$dom->find('#s-refinements #priceRefinements', 0)->remove();
		}


		$div = $dom->find('#s-refinements #departments', 0);
		$title = $div->find('.a-section.a-spacing-small span.a-size-base.a-color-base.puis-bold-weight-text', 0)->plaintext;

		$refinementsList = new stdClass();
		$refinementsList->title = $title;
		$refinementsList->links = [];

		if ($title !== 'Delivery' && $title !== 'Delivery Day') {
			foreach ($div->find('li > span > a') as $a) {
				if ($a->find('.a-size-base.a-color-base', 0)) {
					$name = $a->find('.a-size-base.a-color-base', 0)->plaintext;
					$stars = '';

					if (!$name) {
						if ($a->find('section', 0)) {
							$stars = $a->find('section', 0)->getAttribute('aria-label');
						}

						if ($stars) {
							if (strpos($stars, 'Stars & Up') !== false) {
								$stars = preg_replace('/(\d+)\s*Stars\s*&\s*Up/', '$1 Estrellas o más', $stars);
							} elseif (strpos($stars, 'Star & Up') !== false) {
								$stars = preg_replace('/(\d+)\s*Star(s)?\s*&\s*Up/', '$1 Estrella$2 o más', $stars);
							}

							$name = $stars;
						}
					}

					$link = $a->href;
					$checked = (bool) $a->find('.a-text-bold', 0);

					$l = new stdClass();
					$l->name = $name;
					$l->link = $link;
					$l->checked = $checked;
					$refinementsList->links[] = $l;
				}
			}
		}
		$refinementsBase[] = $refinementsList;

		$div = $dom->find('#s-refinements #reviewsRefinements', 0);
		$title = $div->find('.a-section.a-spacing-small span.a-size-base.a-color-base.puis-bold-weight-text', 0)->plaintext;

		$refinementsList = new stdClass();
		$refinementsList->title = $title;
		$refinementsList->links = [];

		if ($title !== 'Delivery' && $title !== 'Delivery Day') {
			foreach ($div->find('li > span > a') as $a) {
				if ($a->find('.a-icon-alt', 0)) {
					$name = trim($a->find('.a-icon-alt', 0)->plaintext);
					$stars = '';

					if (!$name) {
						if ($a->find('section', 0)) {
							$stars = $a->find('section', 0)->getAttribute('aria-label');
						}

						if ($stars) {
							if (strpos($stars, 'Stars & Up') !== false) {
								$stars = preg_replace('/(\d+)\s*Stars\s*&\s*Up/', '$1 Estrellas o más', $stars);
							} elseif (strpos($stars, 'Star & Up') !== false) {
								$stars = preg_replace('/(\d+)\s*Star(s)?\s*&\s*Up/', '$1 Estrella$2 o más', $stars);
							}

							$name = $stars;
						}
					}

					$link = $a->href;
					$checked = (bool) $a->find('.a-text-bold', 0);

					$l = new stdClass();
					$l->name = $name;
					$l->link = $link;
					$l->checked = $checked;
					$refinementsList->links[] = $l;
				}
			}
		}

		$refinementsBase[] = $refinementsList;

		$div = $dom->find('#s-refinements #brandsRefinements', 0);

		if ($div) {
			$title = $div->find('.a-section.a-spacing-small span.a-size-base.a-color-base.puis-bold-weight-text', 0)->plaintext;

			$links = [];

			$refinementsList = new stdClass();
			$refinementsList->title = $title;
			$refinementsList->links = [];

			if ($title !== 'Delivery' && $title !== 'Delivery Day') {
				foreach ($div->find('li > span > a') as $a) {
					if ($a->find('.a-size-base.a-color-base', 0)) {
						$name = $a->find('.a-size-base.a-color-base', 0)->plaintext;
						$stars = '';

						if (!$name) {
							if ($a->find('section', 0)) {
								$stars = $a->find('section', 0)->getAttribute('aria-label');
							}

							if ($stars) {
								if (strpos($stars, 'Stars & Up') !== false) {
									$stars = preg_replace('/(\d+)\s*Stars\s*&\s*Up/', '$1 Estrellas o más', $stars);
								} elseif (strpos($stars, 'Star & Up') !== false) {
									$stars = preg_replace('/(\d+)\s*Star(s)?\s*&\s*Up/', '$1 Estrella$2 o más', $stars);
								}

								$name = $stars;
							}
						}

						$link = $a->href;
						$checked = (bool) $a->find('.a-text-bold', 0);

						$l = new stdClass();
						$l->name = $name;
						$l->link = $link;
						$l->checked = $checked;
					}

					$refinementsList->links[] = $l;
				}
			}
		}
		$refinementsBase[] = $refinementsList;

		$refinements = new Refinements();
		$ref = new Refinement();
		$ref->setTitle('Precio');
		$links = new RefinementLinks();
		foreach ($priceRefinements as $l) {
			$link = new RefinementLink();
			$link->setName($l['name']);
			$link->setLink($l['link']);
			$link->setChecked($l['checked']);
			$links->setLink($link);
		}

		$ref->setLinks($links);
		$refinements->setRefinements($ref);

		foreach ($otherRefinements as $refinement) {
			$ref = new Refinement();
			$ref->setTitle($refinement['title']);
			$links = new RefinementLinks();
			if (isset($refinement['links']) && count($refinement['links'])) {
				foreach ($refinement['links'] as $l) {
					$link = new RefinementLink();
					$link->setName($l['name']);
					$link->setLink($l['link']);
					$link->setChecked($l['checked']);
					$links->setLink($link);
				}
				$refinements->setRefinements($ref);
			}
		}

		foreach ($refinementsBase as $refinement) {
			$ref = new Refinement();
			$ref->setTitle($refinement->title);
			$links = new RefinementLinks();
			if (isset($refinement->links) && count($refinement->links)) {
				foreach ($refinement->links as $l) {
					$link = new RefinementLink();
					$link->setName($l->name);
					$link->setLink($l->link);
					$link->setChecked($l->checked);
					$links->setLink($link);
				}
				$refinements->setRefinements($ref);
			}
		}

		return $refinements;
	}
}
