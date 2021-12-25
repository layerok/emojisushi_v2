<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Messages\OrderPrepareChange;
use Layerok\TgMall\Classes\Traits\Lang;

class PreparePaymentChangeHandler extends CallbackQueryHandler
{
    use Lang;
    public function handle()
    {
        \Telegram::sendMessage([
            'text' => self::lang('payment_change'),
            'chat_id' => $this->update->getChat()->id
        ]);
        $this->state->setMessageHandler(OrderPrepareChange::class);
    }
}
