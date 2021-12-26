<?php

namespace Layerok\TgMall\Classes\Utils;

use Layerok\TgMall\Models\State;
use Lovata\BaseCode\Classes\Helper\PosterProducts;
use Lovata\BaseCode\Classes\Helper\PosterUtils;
use Lovata\BaseCode\Models\Branches;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\PaymentMethod;
use OFFLINE\Mall\Models\ShippingMethod;

class CheckoutUtils
{

    public static function getClientAddress(State $state)
    {
        return $state->getOrderInfoAddress();
    }

    public static function getComment(State $state)
    {
        return $state->getOrderInfoComment();
    }

    public static function getPosterComment(State $state): string
    {
        return PosterUtils::getComment([
            'comment' => $state->getOrderInfoComment(),
            'payment_method_name' => self::getPaymentMethodName($state),
            'delivery_method_name' => self::getDeliveryMethodName($state),
            'change' => self::getChange($state)
        ]);
    }

    public static function getDeliveryMethodName(State $state)
    {
        $delivery = ShippingMethod::find($state->getOrderInfoDeliveryMethodId())->first();
        return $delivery->name;
    }

    public static function getPaymentMethodName(State $state)
    {
        $payment_method = PaymentMethod::find($state->getOrderInfoPaymentMethodId())
            ->first();

        return $payment_method->name;
    }

    public static function getSpotId(Branches $branch)
    {
        return $branch->getTabletId();
    }

    public static function getPhone(Customer $customer, State $state)
    {
        $stateData = $state->state['order_info'];

        if (!isset($stateData->state['order_info']) && !isset($stateData['phone'])) {
            return $customer->tg_phone;
        }

        return $stateData['phone'];
    }

    public static function getProducts(Cart $cart, State $state):array
    {
        $products = $cart->products()->get();

        $posterProducts = new PosterProducts();

        return $posterProducts
            ->addCartProducts($products)
            ->addSticks(
                $state->getOrderInfoSticksCount()
            )->all();
    }

    public static function getFirstName(Customer $customer)
    {
        if (!empty($customer->firstname)) {
            return $customer->firstname;
        }
        return null;
    }

    public static function getLastName(Customer $customer)
    {
        if (!empty($customer->lastname)) {
            return $customer->lastname;
        }

        return null;
    }

    public static function getChange(State $state)
    {
        return $state->getOrderInfoChange();
    }



}
