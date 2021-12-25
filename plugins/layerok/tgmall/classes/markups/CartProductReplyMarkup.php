<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class CartProductReplyMarkup
{
    use Lang;


    public static function getKeyboard($id, $quantity, $totalPrice)
    {
        $k = new Keyboard();
        $k->inline();

        $btn1 = $k::inlineButton([
            'text' => self::lang('minus'),
            'callback_data' => json_encode([
                'name' => 'cart',
                'arguments' => [
                    'type' => 'add',
                    'id' => $id,
                    'qty' => -1
                ]
            ]),
        ]);

        $btn2 = $k::inlineButton([
            'text' => $quantity,
            'callback_data' => json_encode([
                'name' => Constants::NOOP
            ])
        ]);

        $btn3 = $k::inlineButton([
            'text' => self::lang('plus'),
            'callback_data' => json_encode([
                'name' => 'cart',
                'arguments' => [
                    'type' => 'add',
                    'id' => $id,
                    'qty' => 1
                ]
            ]),
        ]);

        $btn4 = $k::inlineButton([
            'text' => self::lang('del'),
            'callback_data' => json_encode([
                'name' => 'cart',
                'arguments' => [
                    'type' => 'remove',
                    'id' => $id,
                ]
            ]),
        ]);

        $k->row($btn1, $btn2, $btn3, $btn4);

        $k->row($k::inlineButton([
            'text' => self::lang('price') . ': ' . $totalPrice,
            'callback_data' => json_encode([
                'name' => Constants::NOOP
            ])
        ]));

        return $k;
    }

}
