<?php

namespace Layerok\TgMall\Classes\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Markups\PaymentMethodsReplyMarkup;
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

        $this->state->setOrderInfoPhone($this->text);

        $this->customer->tg_phone = $this->text;
        $this->customer->save();


        $this->telegram->sendMessage([
            'text' => self::lang('chose_payment_method'),
            'chat_id' => $this->chat->id,
            'reply_markup' => PaymentMethodsReplyMarkup::getKeyboard()
        ]);
        $this->state->setMessageHandler(null);
    }

    public function handleErrors()
    {
        foreach ($this->errors as $error) {
            $this->telegram->sendMessage([
                'text' => $error . '. Попробуйте снова.',
                'chat_id' => $this->chat->id
            ]);
        }
    }
}
