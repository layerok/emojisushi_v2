<?php

namespace Lovata\BaseCode\Classes\Seeders;

use October\Rain\Database\Updates\Seeder;
use GuzzleHttp\Client;
use OFFLINE\Mall\Models\ImageSet;
use OFFLINE\Mall\Models\Product;
use poster\src\PosterApi;
use System\Models\File;


class ImageSetTableSeeder extends Seeder
{
    public function run()
    {
        ImageSet::truncate();
        File::truncate();

        $endpoint = "http://emojisushi.com.ua/api/products/images";
        $client = new Client();


        $response = $client->request('GET', $endpoint, ['query' => []]);

        // url will be: http://my.domain.com/test.php?key1=5&key2=ABC;

        $statusCode = $response->getStatusCode();
        $content = json_decode($response->getBody(), true);

        foreach($content as $i){

            if(!isset($i['poster_id'])) continue;

            $poster_id = $i['poster_id'];
            $ingredients = $i['ingredients'];
            $name = $i['name'];

            $product = Product::where('poster_id', $poster_id)->first();
            if(!isset($product)) continue;

            if(isset($ingredients) && !empty($ingredients)){
                $product->description = $ingredients;
                $product->name = $name;
                $product->save();
            }

            if (!isset($i['image'])) {
                // Если на сайте нет изображения для товара, возьмем с постера
                PosterApi::init();
                $res = (object)PosterApi::menu()->getProduct([
                    'product_id' => $poster_id
                ]);

                if ($res->response !== false) {
                    if(!isset($res->response->photo) || empty($res->response->photo)){
                        // Если даже на постере отсутствует изображение
                        continue;
                    }
                    $i['image'] = $_ENV['POSTER_URL'] . $res->response->photo;
                }

            };




            // todo: проверить существует ли это изображение конкретно для товара
            // todo: проверять содержимое изображение






            $url = $i['image'];

            $image_set = ImageSet::create([
                'name' => $name,
                'is_main_set' => 1,
                'product_id' => $product['id'],
            ]);

            $file = new File;
            $file->fromUrl($url);

            if(!isset($file)) continue;

            $image_set->images()->add($file);


        }


    }
}


