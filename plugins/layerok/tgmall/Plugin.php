<?php
namespace Layerok\TgMall;

use Monolog\Formatter\LineFormatter;
use OFFLINE\Mall\Models\Customer;
use System\Classes\PluginBase;

class Plugin extends PluginBase
{
    public $require = ['Offline.Mall'];


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
