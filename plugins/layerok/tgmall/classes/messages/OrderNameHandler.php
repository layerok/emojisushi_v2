<?php

namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Classes\Markups\IsRightPhoneReplyMarkup;
use Layerok\TgMall\Classes\Markups\PaymentMethodsReplyMarkup;
use Layerok\TgMall\Classes\Markups\YesNoReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\PaymentMethod;
use Telegram\Bot\Keyboard\Keyboard;

class OrderNameHandler extends AbstractMessageHandler
{
    use Lang;

    public function handle()
    {

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
}
