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
            $last = $photoObject->last();

            if (!isset($last)) {
                return;
            }

            $file_id = $last['file_id'];

            if (is_null($product->image)) {
                return;
            }

            if (!is_null($product->image->file_id)) {
                return;
            }

            $product->image->file_id = $file_id;
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
