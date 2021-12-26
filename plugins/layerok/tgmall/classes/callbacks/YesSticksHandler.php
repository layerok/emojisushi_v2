<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\SticksCounterReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class YesSticksHandler extends CallbackQueryHandler
{
    use Lang;
    public function handle()
    {
        $this->telegram->sendMessage([
            'text' => 'Добавьте желаемое кол-во палочек',
            'reply_markup' => SticksCounterReplyMarkup::getKeyboard(1),
            'chat_id' => $this->update->getChat()->id,
        ]);
    }
}
