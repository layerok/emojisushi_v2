<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\PaymentMethodsReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class ListPaymentMethodsHandler extends CallbackQueryHandler
{
    use Lang;

    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckNotChosenBranchMiddleware::class
    ];
    public function handle()
    {
        $this->telegram->sendMessage([
            'text' => self::lang('chose_delivery_method'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => PaymentMethodsReplyMarkup::getKeyboard()
        ]);
    }
}
