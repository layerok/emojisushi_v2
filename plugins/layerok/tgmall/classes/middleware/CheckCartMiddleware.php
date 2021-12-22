<?php

namespace Layerok\TgMall\Classes\Middleware;

use Layerok\Tgmall\Classes\Markups\CartEmptyReplyMarkup;
use OFFLINE\Mall\Models\Cart;

class CheckCartMiddleware extends AbstractMiddleware
{
    public function run():bool
    {
        if (!isset($this->customer)) {
            return false;
        }

        $cart = Cart::byUser($this->customer->user);

        if (!isset($cart)) {
            return false;
        }

        $this->products = $cart->products()->get();

        if (!count($this->products) > 0) {
            return false;
        }
        return true;
    }

    public function onFailed():void
    {
        $replyMarkup = new CartEmptyReplyMarkup();
        \Telegram::sendMessage([
            'text' => 'Ваш корзина пуста. Пожалуйста добавьте товар в корзину.',
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => $replyMarkup->getKeyboard()
        ]);
    }
}
