<?php
use Illuminate\Support\Facades\Route;
Route::get('/test-tgmall', function () {

//    $response = \Telegram::getMe();
//
//    $botId = $response->getId();
//    $firstName = $response->getFirstName();
//    $username = $response->getUsername();

//    dd($botId, $firstName, $username);
});

Route::get('/test/logger/emergency', function () {
    \Log::driver('some_non_existent_logger')
        ->debug('emergency debugger was created');
});

Route::get('/test/variable/scope', function () {
    function test1($message)
    {
        return $message . ' changed';
    }
    function test2($message)
    {
        test1($message = test1($message));
        return $message;
    };

    dd(test2('message'));
});

Route::get('/test/logger/message/formatter', function() {

    class TestObj extends \Illuminate\Support\Collection
    {
        public $items = [
            "I am physically and emotionally",
            "attracted to man and women"
        ];
    }

    $test1 = collect(['Why', ' ', 'are', ' ', 'you', ' ', 'gay?']);
    $test2 = ['Who', ' ', 'said', ' ', "I'm", ' ', 'gay?'];
    $test3 = ['you' => ['are'], ['gay']];
    $test4 = new TestObj();

    $context1 = ['So', ' ', 'who', ' ', 'is', ' ', 'gay?'];

    \Log::debug($test1, $context1);
    \Log::debug($test2);
    \Log::debug($test3);
    \Log::debug($test4->toArray());
});

Route::get('/test/logger/channel/stack', function() {
    $message = "some error occurred [actually It didn't. It was sent just for testing purposes]";
    \Log::error($message);
    dd('check email: ' . env('LOG_ERROR_EMAIL_TO'));
});

Route::get('/test/mail/mail', function() {
    $to = 'rudomanenkovladimir@gmail.com';
    $subject = '[Logs] emojisushi.com.ua';
    $message = "some error occurred [actually It didn't]";
    $response = mail($to,$subject, $message);
    dd($response ? 'message was sent to ' . $to: 'message was not sent');
});
