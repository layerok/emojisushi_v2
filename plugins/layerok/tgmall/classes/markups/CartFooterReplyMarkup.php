<?php namespace Layerok\Tgmall\Classes\Markups;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Utils\PriceUtils;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Currency;
use Telegram\Bot\Keyboard\Keyboard;

class CartFooterReplyMarkup
{
    use Lang;

    public static function getKeyboard(Cart $cart)
    {
        $k = new Keyboard();
        $k->inline();

        if ($cart->products->count() !== 0) {
            $k->row($k::inlineButton([
                'text' => str_replace(
                    "*price*",
                    PriceUtils::formattedCartTotal($cart),
                    self::lang('all_amount_order')
                ),
                'callback_data' => json_encode([
                    'name' => Constants::NOOP
                ])
            ]));

            $k->row($k::inlineButton(([
                'text' => self::lang('take_order'),
                'callback_data' => json_encode([
                    'name' => 'checkout'
                ])
            ])));
        }

        $k->row($k::inlineButton([
            'text' => self::lang('in_menu'),
            'callback_data' => json_encode([
                'name' => 'menu'
            ])
        ]));

        $k->row($k::inlineButton([
            'text' => self::lang('in_menu_main'),
            'callback_data' => json_encode([
                'name' => 'start'
            ])
        ]));

        return $k;
    }

}
