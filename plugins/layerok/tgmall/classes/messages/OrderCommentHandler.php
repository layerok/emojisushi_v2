<?php

namespace Layerok\TgMall\Classes\Messages;

use Telegram\Bot\Keyboard\Keyboard;

class OrderCommentHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->mergeOrderInfo([
            'comment' => $this->text
        ]);
        $k = new Keyboard();
        $k->inline();
        $yes = $k::inlineButton([
            'text' => 'Да',
            'callback_data' => json_encode([
                'name' => 'confirm_order'
            ])
        ]);
        $no = $k::inlineButton([
            'text' => 'Нет',
            'callback_data' => json_encode([
                'name' => 'start'
            ])
        ]);
        $k->row($yes, $no);


        \Telegram::sendMessage([
            'chat_id' => $this->chat->id,
            'text' => 'Подтвердить заказ?',
            'reply_markup' => $k
        ]);
        $this->state->setMessageHandler(null);
    }
}
