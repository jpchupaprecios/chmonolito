<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Amazon\Product\Variants;

use App\Models\Product\Variant;
use App\Models\Product\VariantOption;
use DOMXPath;

final class ChapiAmazonVariantsParser
{
	private DOMXPath $xpath;

	public function __construct(DOMXPath $xpath)
	{
		$this->xpath = $xpath;
	}

	public function parse(string $currentAsin, int $productId): array
	{
		$variantNames = $this->getVariantNames();

		$matchingDivs = $this->getMatchingDivs();

		if (empty($variantNames)) {
			$variantNames = $matchingDivs;
		}

		$variants = [];
		foreach ($variantNames as $name) {
			$variantBlock = $this->getVariantBlock($name);
			$variantType = 'default';
			$title = '';

			if ($variantBlock) {
				$title = $this->getVariantTitle($variantBlock);
				if ($this->hasImages($variantBlock)) {
					$variantType = 'image';
				}

				$variantData = [
					'name' => $name,
					'type' => $variantType,
					'title' => utf8_encode($title),
					'options' => $this->processVariants($variantBlock, $currentAsin, $variantType),
				];

				$variants[] = $variantData;
			}
		}

		return $this->formatVariants($variants, $productId);
	}

	private function getVariantNames(): array
	{
		$variantNamesElement = $this->xpath->query('//input[@name="twisterDimKeys"]')->item(0);
		return $variantNamesElement ? explode(',', $variantNamesElement->getAttribute('value')) : [];
	}

	private function getMatchingDivs(): array
	{
		$matchingDivs = [];
		$divs = $this->xpath->query('//div');

		foreach ($divs as $div) {
			$id = $div->getAttribute('id');
			if ($id && (preg_match('/^inline-twister-row-[a-zA-Z_]+$/', $id) || preg_match('/^variation_[a-zA-Z_]+$/', $id))) {
				$modifiedId = preg_replace('/^(inline-twister-row-|variation_)/', '', $id);
				$matchingDivs[] = $modifiedId;
			}
		}

		return $matchingDivs;
	}

	private function getVariantBlock(string $name)
	{
		$variantBlock = $this->xpath->query("//div[@id='variation_$name']")->item(0);
		if (!$variantBlock) {
			$variantBlock = $this->xpath->query("//div[@id='inline-twister-row-$name']")->item(0);
		}
		return $variantBlock;
	}

	private function getVariantTitle($variantBlock): string
	{
		$titleElement = $this->xpath->query('.//label | .//span[contains(@class, "dimension-text")]', $variantBlock)->item(0);
		if ($titleElement) {
			$title = trim(($titleElement->textContent));
			if (strpos($title, ':') !== false) {
				$title = trim(explode(':', $title)[0]);
			}
			return trim($title, ':');
		}
		return '';
	}

	private function hasImages($variantBlock): bool
	{
		return $this->xpath->query('.//img', $variantBlock)->length > 0;
	}

	private function processVariants($variantBlock, string $currentAsin, string $variantType = 'default'): array
	{
		$variantData = [];
		$options = $this->xpath->query('.//li | .//option', $variantBlock);

		foreach ($options as $option) {
			$asin = $option->getAttribute('data-csa-c-item-id') ? $option->getAttribute('data-csa-c-item-id') : $this->getAsinFromDataDpUrl($option);

            if(!$asin){
                $asin = $option->getAttribute('value') ?? '';
                if($asin === "-1"){
                    continue;
                }
                if($asin && strpos($asin, ',') !== false){
                    $asin = explode(',', $asin)[1];
                }
            }

            if(!$asin){
                continue;
            }

            $isAvailable = strpos($option->getAttribute('class'), 'swatchUnavailable') === false && strpos($option->getAttribute('class'), 'dropdownUnavailable') === false;

			$optionData = [
				'sku' => $asin,
				'available' => $isAvailable,
				'text' => '',
				'img' => null,
				'selected' => $currentAsin === $asin,
			];

			if ($variantType === 'image') {
				$imgElement = $this->xpath->query('.//img', $option)->item(0);
				if ($imgElement) {
					$optionData['img'] = $imgElement->getAttribute('src');
					$optionData['text'] = $imgElement->getAttribute('alt');
				}
			} else {
				$buttonElement = $this->xpath->query('.//button', $option)->item(0);
				$spanElement = $this->xpath->query('.//span[contains(@class, "swatch-title-text-display")]', $option)->item(0);
				if ($buttonElement) {
					$optionData['text'] = trim(($buttonElement->textContent));
				} elseif ($spanElement) {
                    $optionData['text'] = trim(($spanElement->textContent));
                } else {
                    $optionData['text'] = trim(($option->textContent));
                }
			}

			$optionData['text'] = trim(str_replace("\n", '', $optionData['text']));

			if ($optionData['text'] && $optionData['sku']) {
				$variantData[] = $optionData;
			}
		}

		return $variantData;
	}

	private function getAsinFromDataDpUrl($option): ?string
	{
		$dataDpUrl = $option->getAttribute('data-dp-url');
		if ($dataDpUrl) {
			preg_match('/\/dp\/([^\/]+)/', $dataDpUrl, $matches);
			return $matches[1] ?? null;
		}
		return null;
	}

	private function formatVariants(array $variants, int $productId): array
	{
		$result = [];

		foreach ($variants as $variantResult) {
			$variantsGroup = new Variant();
            $variantsGroup->setAttribute('product_details_id', $productId);
			$type = $variantResult['type'] === 'image' ? 'color' : 'default';
			$variantsGroup->setAttribute('type', $type);
			$variantsGroup->setAttribute('title', $variantResult['title']);
            $variantsGroup->save();

			$variantObjs = [];
			foreach ($variantResult['options'] as $variant) {
				if (is_array($variant)) {
					if (trim(strtolower($variant['text'])) === 'seleccionar' || trim(strtolower($variant['text'])) === 'select') {
						continue;
					}
					$variantObj = new VariantOption();
					$variantObj->setAttribute('title', utf8_encode($variant['text']));
					$variantObj->setAttribute('product_id', $variant['sku']);
					$variantObj->setAttribute('image', $variant['img']);
                    $variantObj->setAttribute('selected', $variant['selected']);
                    $variantObj->setAttribute('variant_id', $variantsGroup->id);
					$variantObj->setAttribute('available', $variant['available']);
					$variantObjs[] = $variantObj;
				}
			}

			if (!empty($variantObjs)) {
				$variantsGroup->setRelation('options', $variantObjs);
                $variantsGroup->options()->saveMany($variantObjs);
				$result[] = $variantsGroup;
			}
		}

		return $result;
	}
}
