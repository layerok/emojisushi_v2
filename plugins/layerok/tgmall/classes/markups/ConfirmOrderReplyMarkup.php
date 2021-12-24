<?php

namespace Layerok\TgMall\Classes\Markups;

use Telegram\Bot\Keyboard\Keyboard;

class ConfirmOrderReplyMarkup
{
    public static function getKeyboard():Keyboard
    {
        return YesNoReplyMarkup::getKeyboard(
            [
                'name' => 'confirm_order'
            ],
            [
                'name' => 'start'
            ]
        );
    }

}
