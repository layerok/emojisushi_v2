<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\Webhook;


$botToken = Config::get('layerok.tgmall::botToken');
$webhookUrl = '/webhook' . $botToken;

Route::post($webhookUrl, function () {
    new Webhook();
});

include('test_routes.php');

