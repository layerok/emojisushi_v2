<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Messages\OrderCommentHandler;

class LeaveCommentHandler extends CallbackQueryHandler
{
    public function handle()
    {
        // самовывоз
        \Telegram::sendMessage([
            'text' => 'Комментарий к заказу',
            'chat_id' => $this->update->getChat()->id,
        ]);
        $this->state->setMessageHandler(OrderCommentHandler::class);
    }
}
