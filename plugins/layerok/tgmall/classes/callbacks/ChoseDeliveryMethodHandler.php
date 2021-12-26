<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Markups\LeaveCommentReplyMarkup;
use Layerok\TgMall\Classes\Markups\YesNoReplyMarkup;
use Layerok\TgMall\Classes\Messages\OrderCommentHandler;
use Layerok\TgMall\Classes\Messages\OrderDeliveryAddressHandler;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\ShippingMethod;
use Telegram\Bot\Keyboard\Keyboard;

class ChoseDeliveryMethodHandler extends CallbackQueryHandler
{
    use Lang;
    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckNotChosenBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckEmptyCartMiddleware::class
    ];

    public function handle()
    {
        $id = $this->arguments['id'];
        $this->state->mergeOrderInfo([
            'delivery_method_id' => $id
        ]);

        if ($id == 3) {
            // доставка курьером
            $this->telegram->sendMessage([
                'text' => 'Введите адрес доставки',
                'chat_id' => $this->update->getChat()->id,
            ]);
            $this->state->setMessageHandler(OrderDeliveryAddressHandler::class);

            return;
        }

        // был выбран самовывоз
        $this->telegram->sendMessage([
            'text' => self::lang('leave_comment_question'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => LeaveCommentReplyMarkup::getKeyboard()
        ]);

        $this->state->setMessageHandler(null);
    }
}
