<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\DeliveryMethodsReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class ListDeliveryMethodsHandler extends CallbackQueryHandler
{
    use Lang;

    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];
    public function handle()
    {
        \Telegram::sendMessage([
            'text' => $this->lang('chose_delivery_method'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => DeliveryMethodsReplyMarkup::getKeyboard()
        ]);
    }
}
