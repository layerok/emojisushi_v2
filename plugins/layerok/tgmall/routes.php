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

Route::get('/troll', function() {
/*    $stickers = [
        'CAACAgIAAxkBAAECuTthE3vHvMvx1HTvLCT_7MgeR99VlQACggADNR33Kwm7dTq2GumXIAQ',
        'CAACAgIAAxkBAAECuT1hE3wFaYEUpYbttYRroCsyyk6ihQACgwADNR33K1W8AcEoiQmIIAQ',
        'CAACAgIAAxkBAAECuT5hE3wHISQ3MwjVtB_kgvHZRLDRhAAChAADNR33K6AYGK_3RkZUIAQ',
        'CAACAgIAAxkBAAECuUBhE3wHj5DJLCubpVXDl2oSfiQ8fAAChQADNR33K31i1n2lZ0G_IAQ',
    ];
    \Telegram::sendMessage([
        'chat_id' => '120831782',
        'text' => 'Ну и че интересный Вилл Смитт?'
    ]);*/
    // юРбан 120831782
/*    foreach ($stickers as $sticker) {
        \Telegram::sendSticker([
            'chat_id' => '419586467',
            'sticker' => $sticker
        ]);
    }*/

});

include('tests/routes.php');

