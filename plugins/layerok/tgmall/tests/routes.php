<?php
namespace Layerok\Tests;

use Illuminate\Support\Facades\Route;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;

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

Route::get('/test/log/telegram', function() {
    $eror = eror;
    \Log::channel('telegram')->debug('test telegram');
});

Route::get('/test/log/processor/regex', function() {
$msg = "[2021-12-11 23:08:26] dev.ERROR: ErrorException: Use of undefined constant eror - assumed 'eror' (this will throw an Error in a future version of PHP) in E:\OpenServer\OpenServer\domains\emojisushi_v2\plugins\layerok\tgmall\tests\routes.php:159
Stack trace
#0 E:\OpenServer\OpenServer\domains\emojisushi_v2\plugins\layerok\tgmall\tests\routes.php(159): Illuminate\Foundation\Bootstrap\HandleExceptions->handleError()
#1 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Routing\Route.php(205): System\Classes\PluginManager->Layerok\Tests\{closure}()
#2 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Routing\Route.php(179): Illuminate\Routing\Route->runCallable()
#3 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Routing\Router.php(681): Illuminate\Routing\Route->run()
#4 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php(130): Illuminate\Routing\Router->Illuminate\Routing\{closure}()
#5 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php(105): Illuminate\Pipeline\Pipeline->Illuminate\Pipeline\{closure}()
#6 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Routing\Router.php(683): Illuminate\Pipeline\Pipeline->then()
#7 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Routing\Router.php(658): Illuminate\Routing\Router->runRouteWithinStack()
#8 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Routing\Router.php(624): Illuminate\Routing\Router->runRoute()
#9 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\october\rain\src\Router\CoreRouter.php(20): Illuminate\Routing\Router->dispatchToRoute()
#10 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php(170): October\Rain\Router\CoreRouter->dispatch()
#11 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php(130): Illuminate\Foundation\Http\Kernel->Illuminate\Foundation\Http\{closure}()
#12 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode.php(63): Illuminate\Pipeline\Pipeline->Illuminate\Pipeline\{closure}()
#13 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\october\rain\src\Foundation\Http\Middleware\CheckForMaintenanceMode.php(25): Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode->handle()
#14 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php(171): October\Rain\Foundation\Http\Middleware\CheckForMaintenanceMode->handle()
#15 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Pipeline\Pipeline.php(105): Illuminate\Pipeline\Pipeline->Illuminate\Pipeline\{closure}()
#16 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php(145): Illuminate\Pipeline\Pipeline->then()
#17 E:\OpenServer\OpenServer\domains\emojisushi_v2\vendor\laravel\framework\src\Illuminate\Foundation\Http\Kernel.php(110): Illuminate\Foundation\Http\Kernel->sendRequestThroughRouter()
#18 E:\OpenServer\OpenServer\domains\emojisushi_v2\index.php(43): Illuminate\Foundation\Http\Kernel->handle()
#19 {main}";

$new_msg = preg_replace('/Stack trace.*$/s', '', $msg);

dd($new_msg);
});

Route::get('/test/telegram/edit', function() {
    // Очень забавная вещь, если редачить replyMarkup
    // и в какой-то кнопке забыть указать callback_data
    // или указать пустоту, то сообщение не отредактируется
    $quantity = 1;
    $replyMarkup = [];
    $replyMarkup['inline_keyboard'][0][0] = [
        'text' => 'minus',
        'callback_data' => '/update_qty ' . ($quantity - 1)
    ];
    $replyMarkup['inline_keyboard'][0][1] =[
        'text' => $quantity . '/10',
        'callback_data' => 'fd'
    ];
    $replyMarkup['inline_keyboard'][0][2] =[
        'text' => 'plus',
        'callback_data' => '/update_qty ' . ($quantity + 1)
    ];


    $replyMarkup['inline_keyboard'][1][0] =[
        'text' => "in_basket_button_title",
        'callback_data' => 'some'
    ];

    $k = new Keyboard();
    $k->inline();
    $btn1 = $k::inlineButton([
        'text' => 'minus',
        'callback_data' => '/update_qty ' . ($quantity - 1)
    ]);
    $btn2 = $k::inlineButton([
        'text' => $quantity . '/10',
        'callback_data' => 'fd'
    ]);
    $btn3 = $k::inlineButton([
        'text' => 'plus',
        'callback_data' => '/update_qty ' . ($quantity + 1)
    ]);
    $k->row($btn1, $btn2, $btn3);

    $btn4 = $k::inlineButton([
        'text' => "in_basket_button_title",
        'callback_data' => 'some'
    ]);

    $k->row($btn4);


    echo '<pre>';
    echo json_encode($replyMarkup);
    echo '</pre>';

    echo '<pre>';
    echo $k->toJson();
    echo '</pre>';

    $json = $k->toJson();
    //$json = json_encode($replyMarkup);
   \Telegram::editMessageReplyMarkup([
        'chat_id' => -760193367,
        'message_id' => 1435,
        'reply_markup' => $json
    ]);
});

Route::get('/test/lovata/mall/product', function() {
    $product = Product::find(150);
    dd($product->price()->toArray()['price_formatted']);
});
