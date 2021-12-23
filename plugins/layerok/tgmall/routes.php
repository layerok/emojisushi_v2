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

/*Route::get('clear_system_files', function() {
    \System\Models\File::whereNotNull('file_id')->update(['file_id' => null]);
});*/


/*Route::get('upload', function() {

    if (isset($_GET['url'])) {
        $url = $_GET['url'];
        $response = \Telegram::sendPhoto([
            'photo' => \Telegram\Bot\FileUpload\InputFile::create($url),
            'chat_id' => '-587888839'
        ]);

        dd($response);
    }

    if (isset($_GET['file_id'])) {
        $file_id = $_GET['file_id'];
        $response = \Telegram::sendPhoto([
            'photo' => $file_id,
            'chat_id' => '-587888839'
        ]);

        dd($response);
    }
});*/

include('tests/routes.php');

