<?php

declare(strict_types=1);

namespace App\Helpers;

final class FixEncoding
{
	public static function fix($text)
    {
        if ($text && is_string($text)) {
            $text = trim($text);
            if (mb_detect_encoding($text, 'UTF-8', true) === 'UTF-8') {
                // Convertir de ISO-8859-1 a UTF-8
                $text = utf8_decode($text);

                if(strpos($text, 'á') !== false || strpos($text, 'é') !== false ||  strpos($text, 'í') !== false || strpos($text, 'ó') !== false ||  strpos($text, 'ú') !== false ||  strpos($text, 'ñ') !== false ||  strpos($text, 'Ñ') !== false ||  strpos($text, 'Á') !== false || strpos($text, 'É') !== false || strpos($text, 'Í') !== false || strpos($text, 'Ó') !== false || strpos($text, 'Ú')){
                    return $text;
                }

                return mb_convert_encoding($text, 'UTF-8', 'ISO-8859-1');
            }
        }

        return $text ?? '';
    }
}
