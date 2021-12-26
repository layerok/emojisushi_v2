<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Messages\OrderNameHandler;
use Layerok\TgMall\Classes\Messages\OrderPhoneHandler;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class CheckoutHandler extends CallbackQueryHandler
{
    use Lang;

    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckNotChosenBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckEmptyCartMiddleware::class
    ];

    public function handle()
    {
        // Очищаем инфу о заказе при каждом начатии оформления заказа
        $this->state->setOrderInfo([]);

        $this->telegram->sendMessage([
            'chat_id' => $this->update->getChat()->id,
            'text' => "Введите Ваше имя"
        ]);

        $this->state->setMessageHandler(OrderNameHandler::class);
    }
}
