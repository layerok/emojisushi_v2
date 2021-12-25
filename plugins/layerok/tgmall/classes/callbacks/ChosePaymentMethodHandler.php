<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\DeliveryMethodsReplyMarkup;
use Layerok\TgMall\Classes\Markups\PreparePaymentChangeReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;

class ChosePaymentMethodHandler extends CallbackQueryHandler
{
    use Lang;
    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckNotChosenBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckEmptyCartMiddleware::class
    ];

    public function handle()
    {
        $this->state->mergeOrderInfo([
            'payment_method_id' => $this->arguments['id']
        ]);


        if ($this->arguments['id'] == 4) {
            // наличными
            \Telegram::sendMessage([
                'text' => self::lang('prepare_change_question'),
                'chat_id' => $this->update->getChat()->id,
                'reply_markup' => PreparePaymentChangeReplyMarkup::getKeyboard()
            ]);
            $this->state->setMessageHandler(null);
            return;
        }

        // картой
        \Telegram::sendMessage([
            'text' => self::lang('chose_delivery_method'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => DeliveryMethodsReplyMarkup::getKeyboard()
        ]);





    }
}
