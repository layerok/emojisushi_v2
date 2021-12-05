<?php

use Illuminate\Support\Facades\Route;
use OFFLINE\Mall\Models\Category;
use Layerok\TgMall\Classes\Webhook;

$botToken = Config::get('layerok.tgmall::botToken');
$webhookUrl = '/webhook' . $botToken;

Route::post($webhookUrl, function () {
    new Webhook();
});



Route::get('/test-tgmall', function () {
    //\Log::info(Telegram::getAuthor());
//
//    $action = \DB::table('layerok_tgmall_actions')
//        ->select('action_id')
//        ->first();
//
//    $contact = Contact::where('chat_id', '=', 2)->first();
//
//    $pass = "qweasdqweasd";
//    $user = User::updateOrCreate([
//        'name' => "jonh",
//        'password' => $pass,
//        'password_confirmation' => $pass
//    ]);
//
//    $customer = Customer::updateOrCreate([
//        "tg_chat_id" => '3423',
//        "firstname" => "test",
//        "lastname"  => "test",
//        "tg_username" => "test",
//        "user_id" => 5
//    ]);

//    dd($customer);

//    $arr = [];
//
//    if(isset($arr[0]) && $arr == "clear") {
//        dd($arr[0]);
//    }

//    $msg = Lang::get('layerok.tgmall::lang.telegram.menu');
//    dd($msg);

    $product = \OFFLINE\Mall\Models\Product::find(56);

//    $photoIdOrUrl = $product->image->file_id ?? $product->image->path;
//
//    $product->image->file_id = null;
//    $product->image->save();
//
//
//
//    dd($photoIdOrUrl);

    $products = Category::where('id', '=', 2)->first()->products;

    dd($products);

});
