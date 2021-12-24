<?php

namespace Layerok\TgMall\Classes\Markups;


use Telegram\Bot\Keyboard\Keyboard;

class PreparePaymentChangeReplyMarkup
{
    public static function getKeyboard():Keyboard
    {
        return YesNoReplyMarkup::getKeyboard([
            'name' => 'prepare_payment_change'
        ],[
            'name' => 'list_delivery_methods'
        ]);
    }
}
