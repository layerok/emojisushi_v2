<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryProductReplyMarkup
{
    use Lang;


    /**
     * @param $product null | Product
     * @param $quantity
     */
    public static function getKeyboard(Cart $cart, Product $product, $quantity)
    {
        $money = app(Money::class);
        $defaultCurrency = Currency::$defaultCurrency;

        $k = new Keyboard();
        $k->inline();

        $totalPrice = $money->format(
            $product->price()->price * $quantity,
            null,
            $defaultCurrency
        );

        $priceBtn = $k::inlineButton([
            'text' => self::lang('price') . ": " . $totalPrice,
            'callback_data' => json_encode([
                'name' => 'noop'
            ])
        ]);
        if ($cart->isInCart($product)) {
            $addToCartBtn = $k::inlineButton([
                'text' => self::lang('position_in_basket'),
                'callback_data' => json_encode([
                    'name' => Constants::NOOP
                ])
            ]);
        } else {
            $addToCartBtn = $k::inlineButton([
                'text' => self::lang('add_to_cart'),
                'callback_data' => json_encode([
                    'name' => 'add_product',
                    'arguments' => [
                        'id' => $product['id'],
                        'qty' =>  $quantity
                    ]
                ])
            ]);
        }


        $k->row($priceBtn, $addToCartBtn);

        return $k;
    }


}
