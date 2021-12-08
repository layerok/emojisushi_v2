<?php

use Illuminate\Support\Facades\Route;
use OFFLINE\Mall\Models\Category;
use Layerok\TgMall\Classes\Webhook;

$botToken = Config::get('layerok.tgmall::botToken');
$webhookUrl = '/webhook' . $botToken;

Route::post($webhookUrl, function () {


    \Telegram::addCommand(Layerok\TgMall\Commands\StartCommand::class);
    $updates = \Telegram::getWebhookUpdates();
    \Log::info('--------------------');
    \Log::info('Пришел хук от телеги');
    \Log::info(json_encode($updates, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));

    \Telegram::processCommand($updates);

    //new Webhook();
});



Route::get('/test-tgmall', function () {
    $response = \Telegram::getMe();

    $botId = $response->getId();
    $firstName = $response->getFirstName();
    $username = $response->getUsername();

    dd($botId, $firstName, $username);
});
