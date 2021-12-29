<?php

namespace Lovata\BaseCode\Classes\Seeders;

use Illuminate\Support\Facades\DB;
use October\Rain\Database\Updates\Seeder;
use OFFLINE\Mall\Models\Category;
use poster\src\PosterApi;


class CategoryTableSeeder extends Seeder
{

    public function run()
    {

        // Очищаем предыдущие категории
        Category::truncate();

        PosterApi::init();
        $categories = (object)PosterApi::menu()->getCategories();

        foreach ($categories->response as $category) {
            $poster_id = $category->category_id;
            $slug = $category->category_tag ?? str_slug($category->category_name);


            Category::create([
                'name'          => (string)$category->category_name,
                'slug'          => $slug,
                'poster_id'     => (int)$poster_id,
                'sort_order'    => (int)$category->sort_order
            ]);
        }

        // Удалить связь товар - категория
//        DB::table("offline_mall_category_product")
//            ->whereNotIn('category_id', $updated_ids)
//            ->delete();

        // Удалить связь категории - группа свойств
//        DB::table("offline_mall_category_property_group")
//            ->whereNotIn("category_id", $updated_ids)
//            ->delete();


    }
}
