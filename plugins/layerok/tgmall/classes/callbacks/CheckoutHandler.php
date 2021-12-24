<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Messages\OrderPhoneHandler;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class CheckoutHandler extends CallbackQueryHandler
{
    use Lang;

    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckCartMiddleware::class
    ];

    public function handle()
    {
        // Очищаем инфу о заказе при каждом начатии оформления заказа
        $this->state->setOrderInfo([]);
        $use_another_phone = $this->arguments['use_another_phone'] ?? false;
        $use_saved_phone = $this->arguments['use_saved_phone'] ?? false;

        if ($use_saved_phone) {
            $k = new Keyboard();
            $k->inline();

            $methods = PaymentMethod::orderBy('sort_order', 'ASC')->get();

            $methods->map(function ($item) use ($k) {
                $k->row($k::inlineButton([
                    'text' => $item->name,
                    'callback_data' => json_encode([
                        'name' => 'chose_payment_method',
                        'arguments' => [
                            'id' => $item->id
                        ]
                    ])
                ]));
            });

            \Telegram::sendMessage([
                'text' => $this->lang('chose_payment_method'),
                'chat_id' => $this->update->getChat()->id,
                'reply_markup' => $k
            ]);

            $this->state->setOrderInfo([
                'phone' => $this->customer->tg_phone
            ]);


            return;
        }

        if (isset($this->customer->tg_phone) && !$use_another_phone) {
            $k = new Keyboard();
            $k->inline();
            $yes = $k::inlineButton([
                'text' => "Да",
                'callback_data' => json_encode([
                    'name' => 'checkout',
                    'arguments' => [
                        'use_saved_phone' => true
                    ]
                ])
            ]);
            $no = $k::inlineButton([
                'text' => "Нет",
                'callback_data' => json_encode([
                    'name' => 'checkout',
                    'arguments' => [
                        'use_another_phone' => true
                    ]
                ])
            ]);
            $k->row($yes, $no);
            $this->replyWithMessage([
                'text' => $this->lang('right_phone_number') . ' ' . $this->customer->tg_phone . '?',
                'reply_markup' => $k
            ]);
            return;
        }

        $this->replyWithMessage([
            'text' => 'Введите Ваш телефон'
        ]);
        $this->state->setMessageHandler(OrderPhoneHandler::class);
    }
}
