<?php

namespace Layerok\TgMall\Classes\Markups;

use OFFLINE\Mall\Models\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class IsRightPhoneReplyMarkup
{
    public static function getKeyboard(): Keyboard
    {
        return YesNoReplyMarkup::getKeyboard(
            [
                'name' => 'list_payment_methods'
            ],
            [
                'name' => 'enter_phone'
            ]
        );
    }
}
