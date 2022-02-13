<?php

namespace Layerok\TgMall\Classes\Markups;

use Telegram\Bot\Keyboard\Keyboard;

class AddSticksReplyMarkup
{
    public static function getKeyboard():Keyboard
    {
        return YesNoReplyMarkup::getKeyboard(
            [
                'name' => 'add_sticks',
            ],
            [
                'name' => 'comment_dialog'
            ]
        );
    }
}
