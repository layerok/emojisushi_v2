<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\SticksCounterReplyMarkup;

class UpdateSticksCounter extends CallbackQueryHandler
{
    public function handle()
    {
        $count = $this->arguments['count'];
        if ($count < 1) {
            $count = 1;
        }

        $this->telegram->editMessageReplyMarkup([
            'message_id' => $this->update->getMessage()->message_id,
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => SticksCounterReplyMarkup::getKeyboard($count)
        ]);
    }
}
