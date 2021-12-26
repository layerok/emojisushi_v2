<?php

namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Markups\DeliveryMethodsReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\ShippingMethod;
use Telegram\Bot\Keyboard\Keyboard;

class OrderPrepareChange extends AbstractMessageHandler
{
    use Lang;
    public function handle()
    {
        $this->state->setOrderInfoChange($this->text);

        $this->telegram->sendMessage([
            'text' => self::lang('chose_delivery_method'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => DeliveryMethodsReplyMarkup::getKeyboard()
        ]);

        $this->state->setMessageHandler(null);
    }
}
