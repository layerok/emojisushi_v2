<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\SticksCounterReplyMarkup;

class UpdateSticksCounterHandler extends CallbackQueryHandler
{
    public function handle()
    {
        $count = $this->arguments['count'];
        if ($count < 1) {
            return;
        }

        $this->state->setOrderInfoSticksCount($count);

        try {
            $this->telegram->editMessageReplyMarkup([
                'message_id' => $this->update->getMessage()->message_id,
                'chat_id' => $this->update->getChat()->id,
                'reply_markup' => SticksCounterReplyMarkup::getKeyboard($count)
            ]);
        }
        catch (\Exception $exception) {
            \Log::warning((string)$exception);
        }
    }
}
