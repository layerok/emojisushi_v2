<?php

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Jsonable;
use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\Webhook;
use Layerok\TgMall\Models\Settings;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;

$bot_token = \Config::get('layerok.tgmall::test_bot_token');

$webhookUrl = '/webhook';

Route::post($webhookUrl, function () use ($bot_token) {
    if (!Settings::get('turn_off', env('TG_MALL_TURN_OFF', false))) {
        new Webhook($bot_token);
    };
});

/*Route::get('clear/file/id', function() {
    \System\Models\File::whereNotNull('file_id')->update(['file_id' => null]);
});*/

Route::get('/tgmall/set/webhook', function() use ($bot_token, $webhookUrl) {
    $api = new \Telegram\Bot\Api($bot_token);
    $resp = $api->setWebhook([
        'url' => env('NGROK_URL') . '/webhook?XDEBUG_SESSION_START=1'
    ]);

    dd($resp);
});


include('tests/routes.php');

