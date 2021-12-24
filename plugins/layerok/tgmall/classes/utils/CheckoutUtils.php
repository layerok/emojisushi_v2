<?php

namespace Layerok\TgMall\Classes\Utils;

use Layerok\TgMall\Models\State;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\PaymentMethod;
use OFFLINE\Mall\Models\ShippingMethod;

class CheckoutUtils
{

    public static function prepareData(State $state, Customer $customer, Cart $cart)
    {
        $data = [];
        $stateData = $state->state['order_info'];

        if (!empty($customer->firstname)) {
            $data['first_name'] = $customer->firstname;
        }

        if (!empty($customer->lastname)) {
            $data['last_name'] = $customer->lastname;
        }

        if (isset($stateData['payment_method_id'])) {
            $payment = PaymentMethod::find($stateData['payment_method_id'])->first();
            if (isset($payment)) {
                $data['payment'] = $payment->name;
            }
        }

        if (isset($stateData['delivery_method_id'])) {
            $delivery = ShippingMethod::find($stateData['delivery_method_id'])->first();
            if (isset($delivery)) {
                $data['delivery'] = $delivery->name;
            }
        }

        if (isset($stateData['comment'])) {
            $data['comment'] = $stateData['comment'];
        }

        if (isset($stateData['phone'])) {
            $data['phone'] = $stateData['phone'];
        } else {
            $data['phone'] = $customer->tg_phone;
        }

        if (isset($stateData['change'])) {
            $data['change'] = $stateData['change'];
        }

        if (isset($stateData['address'])) {
            $data['address'] = $stateData['address'];
        }

        $data['spot'] = $customer->branch->name;

        $products = $cart->products()->get();

        $data['products'] = PosterUtils::parseProducts($products);

        $data['total'] = PriceUtils::cartTotal($cart);
        return $data;
    }

}
