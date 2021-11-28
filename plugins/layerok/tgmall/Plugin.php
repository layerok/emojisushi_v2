<?php
namespace Layerok\TgMall;

use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['Offline.Mall'];

    public function register() {
       // $this->app->register('Layerok\TgMall\Providers\TelegramServiceProvider');
    }
}
