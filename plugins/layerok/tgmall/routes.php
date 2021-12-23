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

Route::get('clear_system_files', function() {
    \System\Models\File::whereNotNull('file_id')->update(['file_id' => null]);
});


include('tests/routes.php');

