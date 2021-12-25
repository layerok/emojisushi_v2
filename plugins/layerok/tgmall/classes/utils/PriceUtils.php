<?php

namespace Layerok\TgMall\Classes\Utils;

use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Currency;

class PriceUtils
{
    public static function formattedCartTotal(Cart $cart)
    {
        $money = app(Money::class);
        return $money->format(
            $cart->totals()->totalPostTaxes(),
            null,
            Currency::$defaultCurrency
        );
    }

}
