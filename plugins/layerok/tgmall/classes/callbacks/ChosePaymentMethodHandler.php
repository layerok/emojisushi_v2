<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\ShippingMethod;
use Telegram\Bot\Keyboard\Keyboard;

class ChosePaymentMethodHandler extends CallbackQueryHandler
{
    use Lang;
    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckCartMiddleware::class
    ];

    public function handle()
    {

        $k = new Keyboard();
        $k->inline();

        $methods = ShippingMethod::orderBy('sort_order', 'ASC')->get();

        $methods->map(function ($item) use ($k) {
            $k->row($k::inlineButton([
                'text' => $item->name,
                'callback_data' => json_encode([
                    'name' => 'chose_delivery_method',
                    'arguments' => [
                        'id' => $item->id
                    ]
                ])
            ]));
        });

        if ($this->arguments['id'] == 4) {
            // наличными
            \Telegram::sendMessage([
                'text' => $this->lang('payment_change'),
                'chat_id' => $this->update->getChat()->id
            ]);
            $this->state->setStep(Constants::STEP_PAYMENT_CASH);
            return;
        }

        // картой
        \Telegram::sendMessage([
            'text' => $this->lang('chose_delivery_method'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => $k
        ]);
        $this->state->setStep(Constants::STEP_DELIVERY);




    }
}
