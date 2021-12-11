<?php
namespace Layerok\Tests;

use Illuminate\Support\Facades\Route;
use function MongoDB\BSON\toJSON;

if (env('APP_ENV') === 'production') {
    return;
}
Route::get('/test/telegram/bot/log', function () {

    $response = \Telegram::bot('MyLogBot')->getUpdates();
    \Log::channel('telegram')->error("an error occurred [actually It didn't]");

    dd($response);
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

Route::get('/test/logger/message/formatter', function () {

    class TestObj extends \Illuminate\Support\Collection
    {
        public $items = [
            "I am physically and emotionally",
            "attracted to man and women"
        ];
    }

    $test1 = collect(['Why', ' ', 'are', ' ', 'you', ' ', 'gay?']);
    $test2 = ['Who', ' ', 'said', ' ', "I'm", ' ', 'gay?'];
    $test3 = ['you' => ['are'], ['gay!']];
    $test4 = new TestObj();

    $context1 = ['So', ' ', 'who', ' ', 'is', ' ', 'gay?'];

    \Log::debug($test1, $context1);
    \Log::debug($test2);
    \Log::debug($test3);
    \Log::debug($test4->toArray());
});

Route::get('/test/logger/channel/stack', function () {
    $message = "some error occurred [actually It didn't. It was sent just for testing purposes]";
    \Log::error($message);
    dd('check email: ' . env('LOG_ERROR_EMAIL_TO'));
});

Route::get('/test/mail/mail', function () {
    $to = 'rudomanenkovladimir@gmail.com';
    $subject = '[Logs] emojisushi.com.ua';
    $message = "some error occurred [actually It didn't]";
    $response = mail($to,$subject, $message);
    dd($response ? 'message was sent to ' . $to: 'message was not sent');
});


Route::get('/test/php/class', function () {

    trait HasEvents { }
    trait HasTimestamps { }

    class ParentClass
    {
        use HasEvents;
    }
    class Test extends ParentClass
    {
        public static function getStaticClass()
        {
            return static::class;
        }
    }
    $class = Test::getStaticClass();
    $parents = class_parents($class);
    $concatArrays = $parents + [$class => $class];
    $reversedArray = array_reverse($concatArrays);
    $traits = class_uses(ParentClass::class);
    $traits += [HasTimestamps::class => HasTimestamps::class];

    $arr1 = ['a' , 'c'];
    $arr2 = ['a', 'b'];
    $intersection = array_intersect_key($arr1, $arr2);

    $fillable = [
        'name',
        'title'
    ];

    $attributes = [
        'name' => 'Vova',
        'job'  => 'Eblan'
    ];

    function getFillableFromArray($attributes, $fillable)
    {
        return array_intersect_key($attributes, array_flip($fillable));
    }

    $fillableFromArray = getFillableFromArray($attributes, $fillable);



    dd($fillableFromArray);

});

Route::get('/test/laravel/collection', function() {
    $collection = collect(['entities'=> [
        [
            'type' => 'bot_command',
            'offset' => 0,
            'length' => 7
        ],
        [
            'type' => 'bot_command',
            'offset' => 6,
            'length' => 5
        ]
    ]]);

    $obj = $collection->get('entities', collect());
    if(is_array($obj)) {
        $obj = collect($obj);
        $obj::make();
        $contains = $obj->contains('type', 'bot_command');

    }

    $callback_data = collect([
        'command' => 'start',
        'arguments' => [1, 2]
    ]);

    $arguments = collect($callback_data->get('arguments', []))->implode(' ');

    dd($arguments);



});

