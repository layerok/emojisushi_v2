<?php
namespace Layerok\Tests;

use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\ReplyMarkups\CartProductReplyMarkup;
use Layerok\TgMall\Models\Message;
use Layerok\TgMall\Models\State;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;

if (env('APP_ENV') === 'production') {
    return;
}

