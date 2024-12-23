<?php

declare(strict_types=1);

namespace App\Helpers;

final class NotAllowed
{


    // region Properties

    /**
     * @var string[]
     */
    private array $notAllowedBrands;
    /**
     * @var string[]
     */
    private array $notAllowedKeywords;

    /**
     * @var array
     */
    private array $notAllowedSkus;

    /**
     * @var array|string[]
     */
    private array $normalizedNotAllowedKeywords;

    /**
     * @var string
     */
    private string $vendor;

    /**
     * @var array
     */
    private array $normalizedNotAllowedSkus;

    // endregion

    // region Constructor con palabras y skus restringidos
    /**
     * @param string $vendor
     */
    public function __construct(string $vendor){

        $this->vendor = $vendor;

        $this->notAllowedBrands = [
            "isagenix",
        ];

        $this->notAllowedKeywords = [
            "isagenix",
            "Hemp",
            "cáñamo",
            "canamo",
            "cañamo",
            "canabis",
            "cannabis",
            "sativa",
            "marihuana",
            "cannabinoides",
            "cannabinoide",
            "marijuana",
            "CBD",
            "hachís",
            "hachis",
            "hashish",
            "Noco",
            "pistola",
            "rifle",
            "escopeta",
            "ametralladora",
            "fusil",
            "carabina",
            "silenciador",
            "lanzacohetes",
            "AK-47",
            "AR-15",
            "mira telescópica",
            "mira rifle",
            "óptica rifle",
            "optica rifle",
            "visor rifle",
            "puntero láser",
            "bala",
            "munición",
            "municion",
            "municiónes",
            "casquillo",
            "pólvora",
            "polvora",
            "recarga balas",
            "cartucho",
            "funda bala",
            "plomo munición",
            "plomo munición",
            "pistola aire",
            "rifle aire",
            "pistola BB",
            "rifle BB",
            "balines",
            "pistola CO2",
            ".22 LR",
            ".25 ACP",
            ".32 ACP",
            ".38 Special",
            ".357 Magnum",
            "9mm Parabellum",
            ".40 S&W",
            "Smith Wesson",
            ".45 ACP",
            ".44 Magnum",
            ".22 LR",
            "Remington",
            "Winchester",
            "airsoft",
            ".30-06 Springfield",
            ".300 Winchester Magnum",
            ".50 BMG",
            "Machine Gun",
            "12 Gauge",
            "20 Gauge",
            ".410 Bore",
            "Tactical Knife",
            "rail scope"
        ];

        $this->normalizedNotAllowedKeywords = array_map(function($word) {
            $normalizedWord = strtolower($this->removeAccents(trim($word)));

            $additionalVariants = [];
            if (strpos($normalizedWord, '-') !== false) {
                $additionalVariants[] = str_replace('-', '', $normalizedWord); // Sin guion
                $additionalVariants[] = str_replace('-', ' ', $normalizedWord); // Con espacio
            }

            return array_merge([$normalizedWord], $additionalVariants);
        }, $this->notAllowedKeywords);

        $this->normalizedNotAllowedKeywords = array_unique(array_merge(...$this->normalizedNotAllowedKeywords));

        $this->notAllowedSkus = [
            "amazon" => [
                "B00802FYYW",
                "B0BJ141LSN"
            ],
            "ebay" => [
            ],
            "walmart" => [
            ],
            "homedepot" => [

            ],
        ];

        $this->normalizedNotAllowedSkus = [];

        foreach ($this->notAllowedSkus as $vendorKey => $skus) {
            $this->normalizedNotAllowedSkus[$vendorKey] = array_map(function($sku) {
                return strtolower(trim($sku));
            }, $skus);
        }
    }
    // endregion

    // region Metodos

    /**
     * @param string $brand
     * @return bool
     */
    public function isAllowedByBrand(string $brand): bool {
        $normalizedBrand = strtolower($this->removeAccents(trim($brand)));

        if(in_array($normalizedBrand, $this->notAllowedBrands)){
            return false;
        }

        return true;
    }

    /**
     * @param string $keyword
     * @return bool
     */
    public function isAllowedByKeyword(string $keyword): bool {
        $keyword = str_replace("®", '', $keyword);
        $normalizedKeyword = strtolower($this->removeAccents(trim($keyword)));

        $pattern = '/\b(' . implode('|', array_map('preg_quote', $this->normalizedNotAllowedKeywords)) . ')\b/u';

        if (preg_match($pattern, $normalizedKeyword)) {
            return false;
        }



        foreach($this->normalizedNotAllowedKeywords as $normalizedNotAllowedKeyword){
            $words = explode(' ', $normalizedKeyword); // Divides la cadena en palabras
            if(in_array($normalizedNotAllowedKeyword, $words)) {
                return false;
            }
        }

        return true;
    }

    /**
     * @param string $skuParam
     * @return bool
     */
    public function isAllowedBySku(string $skuParam): bool {
        if(!isset($this->normalizedNotAllowedSkus[$this->vendor]) || empty($this->normalizedNotAllowedSkus[$this->vendor])){
            return true;
        }

        $normalizedSku = strtolower(trim($skuParam));
        $notAllowedSkus = $this->normalizedNotAllowedSkus[$this->vendor];

        if(in_array($normalizedSku, $notAllowedSkus)){
            return false;
        }

        if(strpos($normalizedSku, "|") !== false){
            $skus = explode("|", $normalizedSku);

            foreach($skus as $sku){
                foreach($notAllowedSkus as $notAllowedSku){
                    if ($sku === $notAllowedSku) {
                        return false;
                    }
                }
            }
        }

        return true;
    }

    /**
     * @param string $string
     * @return string
     */
    private function removeAccents(string $string): string {
        if (class_exists('Transliterator')) {
            $transliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC;');
            $result = $transliterator->transliterate($string);

            if(!$result){
                $string = mb_convert_encoding($string, 'UTF-8', 'ISO-8859-1');
                $transliterator = \Transliterator::create('NFD; [:Nonspacing Mark:] Remove; NFC;');
                $result = $transliterator->transliterate($string);
            }

            return $result;
        } else {
            return strtr($string, [
                // Mayúsculas
                'Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A',
                'É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E',
                'Í' => 'I', 'Ì' => 'I', 'Ï' => 'I', 'Î' => 'I',
                'Ó' => 'O', 'Ò' => 'O', 'Ö' => 'O', 'Ô' => 'O',
                'Ú' => 'U', 'Ù' => 'U', 'Ü' => 'U', 'Û' => 'U',
                'Ñ' => 'N', 'Ç' => 'C',
                // Minúsculas
                'á' => 'a', 'à' => 'a', 'ä' => 'a', 'â' => 'a',
                'é' => 'e', 'è' => 'e', 'ë' => 'e', 'ê' => 'e',
                'í' => 'i', 'ì' => 'i', 'ï' => 'i', 'î' => 'i',
                'ó' => 'o', 'ò' => 'o', 'ö' => 'o', 'ô' => 'o',
                'ú' => 'u', 'ù' => 'u', 'ü' => 'u', 'û' => 'u',
                'ñ' => 'n', 'ç' => 'c'
            ]);
        }
    }

    // endregion
}
