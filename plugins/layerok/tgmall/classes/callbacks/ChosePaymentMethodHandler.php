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
        $id = $this->arguments['id'];
        $this->state->setOrderInfoPaymentMethodId($id);


        if ($id == 4) {
            // наличными
            $this->telegram->sendMessage([
                'text' => self::lang('prepare_change_question'),
                'chat_id' => $this->update->getChat()->id,
                'reply_markup' => PreparePaymentChangeReplyMarkup::getKeyboard()
            ]);
            $this->state->setMessageHandler(null);
            return;
        }

        // картой
        $this->telegram->sendMessage([
            'text' => self::lang('chose_delivery_method'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => DeliveryMethodsReplyMarkup::getKeyboard()
        ]);





    }
}
