<?php

namespace Lovata\BaseCode\Classes\Seeders;

use Illuminate\Support\Facades\DB;
use Lovata\BaseCode\Classes\Helper\PosterTransition;
use October\Rain\Database\Updates\Seeder;
use OFFLINE\Mall\Models\ImageSet;
use OFFLINE\Mall\Models\Product;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\ProductPrice;
use OFFLINE\Mall\Models\Property;
use OFFLINE\Mall\Models\PropertyGroup;
use OFFLINE\Mall\Models\PropertyGroupProperty;
use OFFLINE\Mall\Models\PropertyValue;
use OFFLINE\Mall\Models\Variant;
use poster\src\PosterApi;
use System\Models\File;


class ProductTableSeeder extends Seeder
{
    public function run()
    {

        // Очищаем товары
        Product::truncate();
        ProductPrice::truncate();
        PropertyGroup::truncate();
        PropertyValue::truncate();
        Property::truncate();
        Variant::truncate();

        DB::table('offline_mall_category_property_group')->delete();
        DB::table('offline_mall_property_property_group')->delete();

        PosterApi::init();
        $products = (object)PosterApi::menu()->getProducts();
        $transition = new PosterTransition;

        foreach ($products->response as $value) {
            $transition->createProduct($value);
        }
    }
}
