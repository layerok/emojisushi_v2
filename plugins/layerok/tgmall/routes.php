<?php

use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\Webhook;


$botToken = Config::get('layerok.tgmall::botToken');
$webhookUrl = '/webhook' . $botToken;

Route::post($webhookUrl, function () {
    new Webhook();
});


Route::get('/test-tgmall', function () {
    $response = \Telegram::getMe();

    $botId = $response->getId();
    $firstName = $response->getFirstName();
    $username = $response->getUsername();

    dd($botId, $firstName, $username);
});
