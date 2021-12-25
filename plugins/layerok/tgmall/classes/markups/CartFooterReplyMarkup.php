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
    protected $keyboard;


    public function __construct(Cart $cart)
    {

        $k = new Keyboard();
        $k->inline();

        if ($cart->products->count() !== 0) {
            $k->row($k::inlineButton([
                'text' => str_replace(
                    "*price*",
                    PriceUtils::formattedCartTotal($cart),
                    $this->lang('all_amount_order')
                ),
                'callback_data' => json_encode([
                    'name' => Constants::NOOP
                ])
            ]));

            $k->row($k::inlineButton(([
                'text' => $this->lang('take_order'),
                'callback_data' => json_encode([
                    'name' => 'checkout'
                ])
            ])));
        }

        $k->row($k::inlineButton([
            'text' => $this->lang('in_menu'),
            'callback_data' => json_encode([
                'name' => 'menu'
            ])
        ]));

        $k->row($k::inlineButton([
            'text' => $this->lang('in_menu_main'),
            'callback_data' => json_encode([
                'name' => 'start'
            ])
        ]));

        $this->keyboard = $k;
    }

    public function getKeyboard(): Keyboard
    {
        return $this->keyboard;
    }
}
