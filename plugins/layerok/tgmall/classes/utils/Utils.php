<?php

namespace Layerok\TgMall\Classes\Utils;

use OFFLINE\Mall\Models\Product;
use Telegram\Bot\FileUpload\InputFile;

class Utils
{

    public static function setFileIdFromResponse($response, Product $product)
    {
        $photoObject = $response->getPhoto();

        if ($photoObject) {
            if (is_null($product->image)) {
                return;
            }

            if (!is_null($product->image->file_id)) {
                return;
            }

            $first = $photoObject->first();

            if (!isset($first)) {
                return;
            }

            $product->image->file_id = $first['file_id'];
            $product->image->save();
        }
    }

    public static function getPhotoIdOrUrl(Product $product)
    {
        $photoIdOrUrl = is_null($product->image->file_id) ?
            InputFile::create($product->image->path) : $product->image->file_id;
        return $photoIdOrUrl;
    }

    public static function getCaption(Product $product)
    {
        return "<b>" . $product->name . "</b>\n\n" . \Html::strip($product->description);
    }
}
