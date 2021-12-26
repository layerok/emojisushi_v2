<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\Webhook;
use Layerok\TgMall\Models\Settings;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;

$bot_token = \Config::get('layerok.tgmall::bot_token');

$webhookUrl = '/webhook' . $bot_token;

Route::post($webhookUrl, function () use ($bot_token) {
    if (!Settings::get('turn_off', env('TG_MALL_TURN_OFF', false))) {
        new Webhook($bot_token);
    };
});


include('tests/routes.php');

