<?php

namespace Layerok\TgMall\Classes\Messages;

use Layerok\TgMall\Classes\Markups\ConfirmOrderReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Utils\CheckoutUtils;
use Lovata\BaseCode\Classes\Helper\Receipt;
use OFFLINE\Mall\Models\Cart;
use Telegram\Bot\Keyboard\Keyboard;

class OrderCommentHandler extends AbstractMessageHandler
{
    use Lang;
    public function handle()
    {
        $this->state->mergeOrderInfo([
            'comment' => $this->text
        ]);

        $receipt = new Receipt();
        $receipt->headline(self::lang('confirm_order_question'));
        $receipt->make([
            'first_name' => CheckoutUtils::getFirstName($this->customer),
            'last_name' => CheckoutUtils::getLastName($this->customer),
            'phone' => CheckoutUtils::getPhone($this->customer, $this->state),
            'address' => CheckoutUtils::getCLientAddress($this->state),
            'comment' => CheckoutUtils::getComment($this->state),
            'payment_method_name' => CheckoutUtils::getPaymentMethodName($this->state),
            'delivery_method_name' => CheckoutUtils::getDeliveryMethodName($this->state),
            'change' => CheckoutUtils::getChange($this->state),
            'spot_name' => $this->customer->branch->name,
            'products' => CheckoutUtils::getProducts($this->cart)
        ]);


        $this->telegram->sendMessage([
            'chat_id' => $this->chat->id,
            'text' => $receipt->getText(),
            'parse_mode' => 'html',
            'reply_markup' => ConfirmOrderReplyMarkup::getKeyboard()
        ]);

        $this->state->setMessageHandler(null);
    }
}
