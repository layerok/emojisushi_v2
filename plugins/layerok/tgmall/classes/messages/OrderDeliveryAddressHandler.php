<?php

namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Classes\Constants;

class OrderDeliveryAddressHandler extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->mergeOrderInfo([
            'address' => $this->text
        ]);
        // самовывоз
        \Telegram::sendMessage([
            'text' => 'Комментарий к заказу',
            'chat_id' => $this->update->getChat()->id,
        ]);
        $this->state->setMessageHandler(OrderCommentHandler::class);
    }
}
