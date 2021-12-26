<?php

namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Classes\Constants;
use OFFLINE\Mall\Models\ShippingMethod;
use Telegram\Bot\Keyboard\Keyboard;

class OrderPrepareChange extends AbstractMessageHandler
{
    public function handle()
    {
        $this->state->mergeOrderInfo([
            'change' => $this->text
        ]);
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
        $this->telegram->sendMessage([
            'text' => 'Выберите тип доставки',
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => $k
        ]);

        $this->state->setMessageHandler(null);
    }
}
