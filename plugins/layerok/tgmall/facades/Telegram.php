<?php

namespace Layerok\TgMall\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendMessage(string $chatId, string $text, mixed $markup = [])
 * @method static string getAuthor()
 * @method static mixed answerCallbackQuery(string $inlineButtonId, string $text = "")
 *
 * @see \Layerok\TgMall\Classes\Telegram
 **/

class Telegram extends Facade
{
    protected static function getFacadeAccessor():string
    {
        return 'telegram';
    }
}
