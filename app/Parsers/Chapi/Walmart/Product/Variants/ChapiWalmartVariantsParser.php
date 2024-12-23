<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Walmart\Product\Variants;

use App\Models\Product\Variant;
use App\Models\Product\VariantCombination;
use App\Models\Product\VariantOption;
use DOMXPath;

final class ChapiWalmartVariantsParser
{
    private DOMXPath $xpath;
    /**
     * @var mixed
     */
    private $jsonData;

    public function __construct(DOMXPath $xpath, $jsonData)
    {
        $this->xpath = $xpath;
        $this->jsonData = $jsonData;
    }

    public function parse(string $currentAsin): array
    {
        $variantsNames = $this->getVariantNames();
        $variantCombinations = [];
        $variants = [];
        if($this->jsonData && isset($this->jsonData["variantCriteria"])){
            foreach($this->jsonData["variantCriteria"] as $variantCriteria){
                foreach($variantsNames as $variantNames){
                    if($variantCriteria["name"] == $variantNames) {
                        $variantType = 'default';
                        if ($variantCriteria["categoryTypeAllValues"] === 'IMAGE_NONTRANSACTIONAL' || $variantCriteria["categoryTypeAllValues"] === 'IMAGE_TRANSACTIONAL') {
                            $variantType = 'image';
                        }

                        $variantData = [
                            'name' => $variantNames,
                            'type' => $variantType,
                            'title' => utf8_encode($variantNames),
                            'options' => $this->processVariants($variantCriteria["variantList"], $currentAsin, $variantType),
                        ];

                        $variants[] = $variantData;

                    }
                }
            }

            foreach($this->jsonData["variantProductIdMap"] as $k => $v){
                $variantCombination = new VariantCombination();
                $variantCombination->setAttribute('variant_key', $k);
                $variantCombination->setAttribute('variant_sku', $v);
                $variantCombination->setAttribute('product_details_id', $currentAsin);
                $variantCombinations[] = $variantCombination;
            }
        }

        return $this->formatVariants($variants, $variantCombinations);
    }

    private function getVariantNames(): array
    {
        $names = [];
        if($this->jsonData && isset($this->jsonData["variantCriteria"])){
            foreach($this->jsonData["variantCriteria"] as $variantCriteria){
                $names[] = $variantCriteria["name"];
            }
        }

        return $names;
    }

    private function processVariants($variantsBlock, string $currentAsin, string $variantType = 'default'): array
    {
        $variantData = [];

        foreach ($variantsBlock as $variantBlock) {
            $asin = $variantBlock["id"];

            if (!$asin) {
                continue;
            }

            $isAvailable = ($variantBlock["availabilityStatus"] === "AVAILABLE") ? true : false;
            $selected = false;
            if ($variantBlock["selected"]) {
                $selected = true;
            }

            $image = null;
            if($variantType == 'image'){
                $image = $variantBlock["swatchImageUrl"];
            }

            $optionData = [
                'product_id' => $asin,
                'available' => $isAvailable,
                'title' => $variantBlock["name"],
                'image' => $image,
                'selected' => $selected,
            ];

            $optionData['title'] = trim(str_replace("\n", '', $optionData['title']));

            if ($optionData['title'] && $optionData['product_id']) {
                $variantData[] = $optionData;
            }
        }

        return $variantData;
    }

    private function formatVariants(array $variants, array $variantCombinations): array
    {

        $result = [];

        foreach ($variants as $variantResult) {
            $variantsGroup = new Variant();
            $type = $variantResult['type'] === 'image' ? 'color' : 'default';
            $variantsGroup->setAttribute('type', $type);
            $variantsGroup->setAttribute('title', $variantResult['title']);

            $variantObjs = [];
            foreach ($variantResult['options'] as $variant) {
                if (is_array($variant)) {
                    if (trim(strtolower($variant['title'])) === 'seleccionar' || trim(strtolower($variant['title'])) === 'select') {
                        continue;
                    }
                    $variantObj = new VariantOption();
                    $variantObj->setAttribute('title', utf8_encode($variant['title']));
                    $variantObj->setAttribute('product_id', $variant['product_id']);
                    $variantObj->setAttribute('image', $variant['image']);
                    $variantObj->setAttribute('selected', $variant['selected']);
                    $variantObj->setAttribute('available', $variant['available']);
                    $variantObjs[] = $variantObj;
                }
            }

            if (!empty($variantObjs)) {
                $variantsGroup->setRelation('options', $variantObjs);
                $result[] = $variantsGroup;
            }
        }

        return [
            "variantCombinations" => $variantCombinations,
            "variantsGroup" => $result,
        ];
    }
}
