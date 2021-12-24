<?php
namespace Layerok\TgMall\Classes\Markups;

use Telegram\Bot\Keyboard\Keyboard;

class LeaveCommentReplyMarkup
{
    public static function getKeyboard(): Keyboard
    {
        return YesNoReplyMarkup::getKeyboard(
            [
                'name' => 'leave_comment',
            ],
            [
                'name' => 'pre_confirm_order'
            ]
        );
    }
}
