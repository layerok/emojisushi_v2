<?php namespace Layerok\Tgmall\Classes\Markups;

use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use Telegram\Bot\Keyboard\Keyboard;

class CartEmptyReplyMarkup
{
    use Lang;


    public static function getKeyboard()
    {
        $k = new Keyboard();
        $k->inline();

        $k->row($k::inlineButton([
            'text' => self::lang('in_menu'),
            'callback_data' => json_encode([
                'name' => 'menu'
            ])
        ]));

        $k->row($k::inlineButton([
            'text' => self::lang('in_menu_main'),
            'callback_data' => json_encode([
                'name' => 'start'
            ])
        ]));

        return $k;
    }

}
