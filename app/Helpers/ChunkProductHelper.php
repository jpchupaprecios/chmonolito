<?php

namespace App\Helpers;

use App\Models\ChunkProductLog;

class ChunkProductHelper
{

    public static function setVariantColorChunk($html, $productId, $success = false){
        //self::setChunkLog($html, 'color', $productId, $success);
    }

    public static function setVariantChunk($html, $productId, $success = false){
        //self::setChunkLog($html, 'variant', $productId, $success);
    }

    public static function setImageChunk($html, $productId, $success = false){
        //self::setChunkLog($html, 'image', $productId, $success);
    }

    public static function setPriceChunk($html, $productId, $success = false){
        //self::setChunkLog($html, 'price', $productId, $success);
    }

    public static function setTitleChunk($html, $productId, $success = false){
        //self::setChunkLog($html, 'title', $productId, $success);
    }

    public static function setGlobalChunk($html, $productId, $success = false){
        //self::setChunkLog($html, 'global', $productId, $success);
    }

    private static function setChunkLog($html, $type, $productId, $success){
        $chunkProductLog = new ChunkProductLog();

        $chunkProductLog->sku = $productId;

        $chunkProductLog->status = $success;
        $chunkProductLog->type = $type;
        $class = 'wrapper-c-' . $type;

        $chunkProductLog->chunk_html_output = "<div class='wrapper-c " .$class. " '>";
        $chunkProductLog->chunk_html_output .= $html . "</div>";
        $chunkProductLog->save();
    }

}
