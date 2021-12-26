<?php

namespace Layerok\TgMall\Classes\Markups;

use Telegram\Bot\Keyboard\Keyboard;

class SticksDialogReplyMarkup
{
    public static function getKeyboard():Keyboard
    {
        return YesNoReplyMarkup::getKeyboard(
            [
                'name' => 'yes_sticks',
            ],
            [
                'name' => 'comment_dialog'
            ]
        );
    }
}
