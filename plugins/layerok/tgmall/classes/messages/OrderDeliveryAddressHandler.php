<?php

namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Markups\SticksDialogReplyMarkup;
use Layerok\TgMall\Classes\Markups\LeaveCommentReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;

class OrderDeliveryAddressHandler extends AbstractMessageHandler
{
    use Lang;
    public function handle()
    {
        $this->state->setOrderInfoAddress($this->text);

        $this->telegram->sendMessage([
            'text' => self::lang('add_sticks_question'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => SticksDialogReplyMarkup::getKeyboard()
        ]);

        $this->state->setMessageHandler(null);
    }
}
