<?php

namespace Layerok\TgMall\Classes\Markups;

use Telegram\Bot\Keyboard\Keyboard;

class YesNoReplyMarkup
{
    public static function getKeyboard($yesCallbackData, $noCallbackData)
    {
        $k = new Keyboard();
        $k->inline();
        $yes = $k::inlineButton([
            'text' => 'Да',
            'callback_data' => json_encode($yesCallbackData)
        ]);
        $no = $k::inlineButton([
            'text' => 'Нет',
            'callback_data' => json_encode($noCallbackData)
        ]);
        $k->row($yes, $no);

        return $k;
    }
}
