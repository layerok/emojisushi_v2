<?php

namespace Layerok\TgMall\Facades;

use Layerok\TgMall\Classes\Telegram as TelegramBase;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array sendMessage(string $text, mixed $markup = [])
 * @method static string getAuthor()
 * @method static mixed answerCallbackQuery(string $inlineButtonId, string $text = "")
 * @method static mixed sendPhoto(string $fileId, string $caption = "", array $markup = [])
 * @method static TelegramBase setChatId(string $chatId)
 * @method static string|null getChatId()
 * @see TelegramBase
 **/

class Telegram extends Facade
{
    protected static function getFacadeAccessor():string
    {
        return 'telegram';
    }
}
