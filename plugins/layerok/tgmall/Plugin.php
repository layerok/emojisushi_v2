<?php
namespace Layerok\TgMall;


use System\Classes\PluginBase;
use Request;

class Plugin extends PluginBase
{
    public $require = ['Offline.Mall'];

    public function boot()
    {
        // to change host for assets
        if (!empty(env('NGROK_URL')) && Request::instance()->server->has('HTTP_X_ORIGINAL_HOST')) {
            $this->app['url']->forceRootUrl(env('NGROK_URL'));
        }
    }

    public function register()
    {
    }

    public function registerSettings()
    {
        return [
            'settings' => [
                'label' => 'EmojiSushiBot settings',
                'description' => 'Manage bot settings.',
                'category' => 'Telegram',
                'icon' => 'icon-cog',
                'class' => \Layerok\TgMall\Models\Settings::class,
                'order' => 500,
                'keywords' => 'telegram bot',
            ]
        ];
    }


}
