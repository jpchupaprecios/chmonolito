<?php

declare(strict_types=1);

namespace App\Parsers\Chapi\Ebay\Product\Variants;

use App\Models\Product\Variant;
use App\Models\Product\VariantOption;
use DOMXPath;
use App\Models\Product\VariantCombination;
final class ChapiEbayVariantsParser
{
    /**
     * @var DOMXPath
     */
    private $dom;

    public function __construct($domDocument = null)
    {
        if ($domDocument) {
            $this->dom = $domDocument;
        }
    }

    public function parse($currentAsin, int $productId): array
    {
        $validData = [];
        $variants = [];
        $variantsData = null;
        $variantCombinations = [];
        $scripts = $this->dom->query("//script");
        if(!$scripts){
            return [];
        }
        foreach ($scripts as $script) {
            $scriptContent = $script->textContent;
            if (strpos($scriptContent, 'raptor') !== false) {
                $scriptContent = str_replace(["\r", "\n", '\n', '\r'], '', $scriptContent);
                preg_match('/\'itmVarModel\':\s*({.*?}),\s*\'almostGoneContent\':/', $scriptContent, $matches);
                if (isset($matches[1])) {
                    $variantsData = json_decode($matches[1], true);
                }
            }

            if (strpos($scriptContent, '$vim_C=(window.$vim_C||[]).concat(') !== false) {
                $scriptContent = str_replace(["\r", "\n", '\n', '\r'], '', $scriptContent);
                $startPos = strpos($scriptContent, '$vim_C=(window.$vim_C||[]).concat(');
                if ($startPos !== false) {
                    $scriptContent = substr($scriptContent, $startPos + strlen('$vim_C=(window.$vim_C||[]).concat('));
                    $scriptContent = trim($scriptContent, ')</script>');
                    $scriptContent = $this->json_unescape_unicode($scriptContent);
                    $scriptContent = $this->remove_html_tags($scriptContent);
                    $variantsVariationsDataRes = json_decode($scriptContent, true);
                    if ($variantsVariationsDataRes && isset($variantsVariationsDataRes['p']) && $variantsVariationsDataRes['p'] === 'MSKU') {
                        $variantsVariationsData = [
                            'g' => $variantsVariationsDataRes['g'],
                            'w' => $variantsVariationsDataRes['w'],
                        ];
                        break;
                    }
                }
            }
        }

        if ($variantsData) {
            foreach ($variantsData['itemVariationsMap'] as $variationId => $variantData) {
                if (isset($variantData['priceInMoney'])) {
                    $validData[] = [
                        'variationId' => $variationId,
                        'price' => $variantData['priceInMoney']['valueInMinorUnits'] / 100,
                    ];
                }
            }
        } else {
            $scripts = $this->dom->query("//script");
            foreach ($scripts as $script) {
                $scriptContent = $script->textContent;
                if (strpos($scriptContent, 'window.$ebay') !== false) {
                    $scriptContent = str_replace(["\r", "\n", '\n', '\r'], '', $scriptContent);
                    $pos = strpos($scriptContent, ').concat(');
                    if ($pos === false) {
                        $pos = strpos((string) $this->dom->document->saveHTML(), ').concat(');
                        if ($pos) {
                            $scriptContent = substr((string) $this->dom->document->saveHTML(), $pos + strlen(').concat('));
                        } else {
                            continue;
                        }
                    } else {
                        $scriptContent = substr($scriptContent, $pos + strlen(').concat('));
                    }

                    $posEndScript = strpos($scriptContent, ')</script>');
                    if ($posEndScript) {
                        $scriptContent = substr($scriptContent, 0, $posEndScript);
                    } else {
                        $scriptContent = trim($scriptContent, ')');
                    }

                    $scriptContent = $this->json_unescape_unicode($scriptContent);
                    $scriptContent = $this->remove_html_tags($scriptContent);
                    $data = json_decode($scriptContent);

                    if (!$data || !isset($data->o->w)) {
                        return [];
                    }

                    $model = null;
                    foreach ($data->o->w as $m) {
                        foreach ($m as $mod) {
                            if (isset($mod->model)) {
                                $model = $mod->model;
                                break 2;
                            }
                        }
                    }

                    if (!$model) {
                        return [];
                    }

                    if (isset($model->modules->MSKU)) {
                        $msku = $model->modules->MSKU;
                        unset($data);
                        $validData = [
                            'selectMenus' => $msku->selectMenus,
                            'menuItemMap' => $msku->menuItemMap,
                            'selectedVariationId' => $msku->selectedVariationId,
                            'menuItemCombinations' => $msku->menuItemCombinations,
                            'variationCombinations' => $msku->variationCombinations,
                        ];

                        foreach ($validData['variationCombinations'] as $key => $variationCombination) {
                            $variantCombination = new VariantCombination();
                            $variantCombination->setAttribute('variant_key', $key);
                            $variantCombination->setAttribute('variant_sku', $variationCombination);
                            $variantCombination->setAttribute('product_details_id', $currentAsin);
                            $variantCombinations[] = $variantCombination;
                        }

                        $data = $validData;

                        $variants = [];
                        foreach ($data['selectMenus'] as $menu) {
                            $variant = [
                                'id' => $menu->id,
                                'displayLabel' => $menu->displayLabel,
                                'defaultDisplayValue' => $menu->defaultDisplayValue,
                                'values' => [],
                            ];

                            $variantList = [];

                            foreach($menu->menuItemValueIds as $valueId){
                                $selected = false;
                                $available = true;
                                if($menu->selectedValueId == $valueId){
                                    $selected = true;
                                }

                                if (!isset($data['menuItemMap']->{$valueId}->outOfStock) || isset($data['menuItemMap']->{$valueId}->outOfStock) && $data['menuItemMap']->{$valueId}->outOfStock === true) {
                                    $available = false;
                                }

                                $variantList[] = [
                                    'title' => $data["menuItemMap"]->{$valueId}->displayName,
                                    'productId' => $data["menuItemMap"]->{$valueId}->valueId,
                                    'image' => null,
                                    'selected' => $selected,
                                    'available' => $available,
                                ];
                            }

                            $variant["values"] = $variantList;
                            $variants[] = $variant;
                        }

                    }
                    break;
                }
            }
        }

        $result = [];
        foreach ($variants as $variantResult) {
            $variantsGroup = new Variant();

            $variantsGroup->setAttribute('product_details_id', $productId);
            $variantsGroup->setAttribute('type', 'default');
            $variantsGroup->setAttribute('title', $variantResult['displayLabel']);
            $variantsGroup->save();
            $variantObjs = [];

            foreach ($variantResult['values'] as $variant) {
                if (is_array($variant)) {
                    $variantObj = new VariantOption();

                    $variantObj->setAttribute('title', $variant['title']);
                    $variantObj->setAttribute('product_id', $variant['productId']);
                    $variantObj->setAttribute('image', $variant['image'] ?? null);
                    $variantObj->setAttribute('selected', $variant['selected']);
                    $variantObj->setAttribute('variant_id', $variantsGroup->id);
                    $variantObj->setAttribute('available', $variant['available']);
                    $variantObjs[] = $variantObj;
                }
            }

            $variantsGroup->setRelation('options', $variantObjs);
            $variantsGroup->options()->saveMany($variantObjs);

            $result[] = $variantsGroup;

        }

        return [
            "variantCombinations" => $variantCombinations,
            "variantsGroup" => $result,
        ];
    }


    private function json_unescape_unicode($str)
    {
        return preg_replace_callback('/\\\\u([0-9a-fA-F]{4})/', function ($match) {
            return mb_convert_encoding(pack('H*', $match[1]), 'UTF-8', 'UCS-2BE');
        }, $str);
    }

    private function remove_html_tags($str)
    {
        return strip_tags($str);
    }


}
