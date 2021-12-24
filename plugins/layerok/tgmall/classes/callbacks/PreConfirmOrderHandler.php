<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\ConfirmOrderReplyMarkup;
use Layerok\TgMall\Classes\Utils\CheckoutUtils;
use Lovata\BaseCode\Classes\Helper\ReceiptUtils;
use OFFLINE\Mall\Models\Cart;
use Telegram\Bot\Keyboard\Keyboard;

class PreConfirmOrderHandler extends CallbackQueryHandler
{
    public function handle()
    {
        $chat = $this->update->getChat();
        $cart = Cart::byUser($this->customer->user);

        $data = CheckoutUtils::prepareData($this->state, $this->customer, $cart);
        $message = ReceiptUtils::makeReceipt('Подтверждаете заказ?', $data);

        \Telegram::sendMessage([
            'chat_id' => $chat->id,
            'text' => $message,
            'parse_mode' => 'html',
            'reply_markup' => ConfirmOrderReplyMarkup::getKeyboard()
        ]);
        $this->state->setMessageHandler(null);
    }
}
