<?php

namespace Layerok\TgMall\Classes\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Markups\IsRightPhoneReplyMarkup;
use Layerok\TgMall\Classes\Markups\PaymentMethodsReplyMarkup;
use Layerok\TgMall\Classes\Markups\YesNoReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class OrderNameHandler extends AbstractMessageHandler
{
    use Lang;

    protected $errors;

    public function validate(): bool
    {
        $data = [
            'firstname' => $this->text
        ];

        $rules = [
            'firstname' => 'required|min:2',
        ];

        $messages = [
            'firstname.required' => "Имя обязательно для заполнения",
            'firstname.min' => "Имя должно содержать минимум :min символа"
        ];

        $validation = Validator::make($data, $rules, $messages);

        if ($validation->fails()) {
            $this->errors = $validation->errors()->get('firstname');
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

        $this->customer->firstname = $this->text;
        $this->customer->save();

        if (isset($this->customer->tg_phone)) {
            $this->telegram->sendMessage([
                'text' => self::lang('right_phone_number') . ' ' . $this->customer->tg_phone . '?',
                'reply_markup' => IsRightPhoneReplyMarkup::getKeyboard(),
                'chat_id' => $this->update->getChat()->id
            ]);
            return;
        }

        $this->telegram->sendMessage([
            'text' => 'Введите Ваш телефон',
            'chat_id' => $this->update->getChat()->id
        ]);
        $this->state->setMessageHandler(OrderPhoneHandler::class);
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
