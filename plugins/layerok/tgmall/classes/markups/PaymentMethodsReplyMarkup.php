<?php

namespace Layerok\TgMall\Classes\Markups;

use OFFLINE\Mall\Models\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class PaymentMethodsReplyMarkup
{
    public static function getKeyboard():Keyboard
    {
        $k = new Keyboard();
        $k->inline();

        $methods = PaymentMethod::orderBy('sort_order', 'ASC')->get();

        $methods->map(function ($item) use ($k) {
            $k->row($k::inlineButton([
                'text' => $item->name,
                'callback_data' => json_encode([
                    'name' => 'chose_payment_method',
                    'arguments' => [
                        'id' => $item->id
                    ]
                ])
            ]));
        });

        return $k;
    }
}
