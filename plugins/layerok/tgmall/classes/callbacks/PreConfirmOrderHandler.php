<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\ConfirmOrderReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Utils\CheckoutUtils;
use Layerok\TgMall\Classes\Utils\PriceUtils;
use Lovata\BaseCode\Classes\Helper\Receipt;
use OFFLINE\Mall\Models\Cart;
use Telegram\Bot\Keyboard\Keyboard;

class PreConfirmOrderHandler extends CallbackQueryHandler
{
    use Lang;
    public function handle()
    {
        $chat = $this->update->getChat();

        $products = CheckoutUtils::getProducts($this->cart);
        $phone = CheckoutUtils::getPhone($this->customer, $this->state);
        $firstName = CheckoutUtils::getFirstName($this->customer);
        $lastName = CheckoutUtils::getLastName($this->customer);
        $address = CheckoutUtils::getCLientAddress($this->state);

        $receipt = new Receipt();
        $receipt->headline(self::lang('confirm_order_question'));
        $receipt->make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'address' => $address,
            'comment' => CheckoutUtils::getComment($this->state),
            'payment_method_name' => CheckoutUtils::getPaymentMethodName($this->state),
            'delivery_method_name' => CheckoutUtils::getDeliveryMethodName($this->state),
            'change' => CheckoutUtils::getChange($this->state),
            'spot_name' => $this->customer->branch->name,
            'products' => $products,
            'total' => PriceUtils::formattedCartTotal($this->cart),
        ]);

        $this->telegram->sendMessage([
            'chat_id' => $chat->id,
            'text' => $receipt->getText(),
            'parse_mode' => 'html',
            'reply_markup' => ConfirmOrderReplyMarkup::getKeyboard()
        ]);
        $this->state->setMessageHandler(null);
    }
}
