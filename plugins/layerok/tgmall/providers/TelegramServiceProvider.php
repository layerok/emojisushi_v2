<?php
namespace Layerok\TgMall\Providers;

use Layerok\TgMall\Classes\Telegram;
use October\Rain\Support\ServiceProvider;

class TelegramServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton('telegram', function ($app) {
            return new Telegram(\Config::get('layerok.tgmall::botToken'));
        });
    }
}
