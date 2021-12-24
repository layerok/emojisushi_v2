<?php

namespace Layerok\TgMall\Classes\Utils;

class PosterUtils
{
    public static function parseProducts($products): array
    {
        $poster_products = [];
        foreach ($products as $p) {
            $productData = [];
            if (isset($p['variant_id'])) {
                $product = $p->product()->first();
                $variant = $p->getItemDataAttribute();
                $productData['modificator_id'] = $variant['poster_id'];
            } else {
                $product = $p->getItemDataAttribute();
            }
            $productData['name'] = $product['name'];
            $productData['product_id'] = $product['poster_id'];
            $productData['count'] = $p['quantity'];

            $poster_products[] = $productData;
        }
        return $poster_products;
    }
}
