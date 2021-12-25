<?php

namespace Layerok\TgMall\Classes\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class OrderPhoneHandler extends AbstractMessageHandler
{
    use Lang;

    protected $errors;

    public function validate(): bool
    {
        $data = [
            'phone' => $this->text
        ];

        $rules = [
            'phone' => 'required|phoneUa',
        ];

        $messages = [
            'phone.required' => "Введите номер телефона",
            'phone.phoneUa' => "Введите номер телефона в формате +38(0xx)xxxxxxx",
        ];

        $validation = Validator::make($data, $rules, $messages);

        if ($validation->fails()) {
            $this->errors = $validation->errors()->get('phone');
            return false;
        }
        return true;
    }

    public function handle()
    {
        $isValid = $this->validate();

        if (!$isValid) {
            $this->handleErrors();
            return;
        }

        $this->state->mergeOrderInfo([
            'phone' => $this->text
        ]);

        $this->customer->tg_phone = $this->text;
        $this->customer->save();

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
            'text' => self::lang('chose_payment_method'),
            'chat_id' => $this->chat->id,
            'reply_markup' => $k
        ]);
        $this->state->setMessageHandler(null);
    }

    public function handleErrors()
    {
        foreach ($this->errors as $error) {
            Telegram::sendMessage([
                'text' => $error . '. Попробуйте снова.',
                'chat_id' => $this->chat->id
            ]);
        }
    }
}
