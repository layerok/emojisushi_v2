<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Messages\OrderCommentHandler;
use Layerok\TgMall\Classes\Messages\OrderPhoneHandler;

class EnterPhoneHandler extends CallbackQueryHandler
{
    public function handle()
    {
        $this->telegram->sendMessage([
            'text' => 'Введите Ваш телефон',
            'chat_id' => $this->update->getChat()->id
        ]);
        $this->state->setMessageHandler(OrderPhoneHandler::class);
    }
}
