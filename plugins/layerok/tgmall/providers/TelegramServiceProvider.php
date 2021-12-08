<?php
namespace Layerok\TgMall\Providers;

use Layerok\TgMall\Classes\Telegram;
use October\Rain\Support\ServiceProvider;
use Telegram\Bot\Api;

class TelegramServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('telegram', function ($app) {
            return new Api(\Config::get('layerok.tgmall::botToken'));
        });
    }
}
