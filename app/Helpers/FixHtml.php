<?php

declare(strict_types=1);

namespace App\Helpers;

final class FixHtml
{
	public static function formatHtml($string){
    if($string){
        $string = str_replace("\n", "", $string);
        $string = str_replace("\t", "", $string);
        $string = trim($string);
    }

    return $string;
}
}
