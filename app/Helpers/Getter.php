<?php

declare(strict_types=1);

namespace App\Helpers;

use Exception;

class Getter
{
	public static function getValueByKeys($data, $keys) {
        foreach ($keys as $key) {
            if (is_array($data) && isset($data[$key])) {
                $data = $data[$key];
            } elseif (is_object($data) && isset($data->$key)) {
                $data = $data->$key;
            } else {
                return null;
            }
        }
        return $data;
    }
}
