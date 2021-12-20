<?php namespace Layerok\Tgmall\Classes\Markups;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Currency;
use Telegram\Bot\Keyboard\Keyboard;

class CartFooterReplyMarkup
{
    use Lang;
    protected $keyboard;
    /**
     * @var Money
     */
    protected $money;

    public function __construct(Cart $cart)
    {
        $this->money = app(Money::class);
        $k = new Keyboard();
        $k->inline();

        if ($cart->products->count() !== 0) {
            $k->row($k::inlineButton([
                'text' => str_replace(
                    "*price*",
                    $this->money->format(
                        $cart->totals()->totalPostTaxes(),
                        null,
                        Currency::$defaultCurrency
                    ),
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
        } else {
            $k->row($k::inlineButton([
                'text' => $this->lang('in_menu'),
                'callback_data' => json_encode([
                    'name' => 'menu'
                ])
            ]));
        }


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
