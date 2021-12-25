<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Classes\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class MainMenuReplyMarkup
{
    use Lang;

    public static function getKeyboard()
    {
        $keyboard = new Keyboard();
        $keyboard->inline();

        $row1 = [];
        $row2 = [];
        $row3 = [];
        $row4 = [];

        $row1[] = $keyboard::inlineButton([
            'text' => self::lang('menu'),
            'callback_data' => json_encode([
                'name' => 'menu',
                'arguments' => []
            ])
        ]);



        //todo
/*        $row2[] = $keyboard::inlineButton([
            'text' => self::lang('delivery_and_pay'),
            'callback_data' => "delivery_and_pay"
        ]);
        $row2[] = $keyboard::inlineButton([
            'text' => self::lang('my_order'),
            'callback_data' => "my_order"
        ]);

        $row3[] = $keyboard::inlineButton([
            'text' => self::lang('review'),
            'callback_data' => "review"
        ]);*/


        $row1[] = $keyboard::inlineButton([
            'text' => self::lang('busket'),
            'callback_data' => json_encode([
                'name' => 'cart',
                'arguments' => [
                    'type' => 'list'
                ]
            ])
        ]);

        $row2[] = $keyboard::inlineButton([
            'text' => self::lang('contact'),
            'callback_data' => json_encode([
                'name' => 'branch_info',
                'arguments' => [
                    'type' => 'phones'
                ]
            ])
        ]);

        $row2[] = $keyboard::inlineButton([
            'text' => '🌐 Вебсайт',
            'callback_data' => json_encode([
                'name' => 'branch_info',
                'arguments' => [
                    'type' => 'website'
                ]
            ])
        ]);

        $row3[] =$keyboard::inlineButton([
            'text' => '👋 Сменить заведение',
            'callback_data' => json_encode([
                'name' => 'list_branches'
            ])
        ]);




        $keyboard->row(...$row1);
        $keyboard->row(...$row2);
        $keyboard->row(...$row3);
        $keyboard->row(...$row4);

        return $keyboard;
    }

}
