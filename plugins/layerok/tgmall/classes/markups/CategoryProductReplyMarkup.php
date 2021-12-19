<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryProductReplyMarkup
{
    use Lang;

    /**
     * @var Keyboard
     */
    protected $keyboard;

    /**
     * @param $product null | Product
     * @param $quantity\
     */
    public function __construct(Product $product, $quantity)
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

        $btn1 = $k::inlineButton([
            'text' => $this->lang('minus'),
            'callback_data' => implode(
                ' ',
                ['/update_qty', $product['id'], ($quantity - 1)]
            )
        ]);
        $btn2 = $k::inlineButton([
            'text' => $quantity,
            'callback_data' => "nope"
        ]);
        $btn3 = $k::inlineButton([
            'text' => $this->lang('plus'),
            'callback_data' => implode(
                ' ',
                ['/update_qty', $product['id'], ($quantity + 1)]
            )
        ]);
        $k->row($btn1, $btn2, $btn3);
        $k->row($k::inlineButton([
            'text' => $this->lang('price') . ": " . $totalPrice . ' ' . $this->lang('add_to_cart'),
            'callback_data' => "/cart add {$product['id']} {$quantity}"
        ]));


        $this->keyboard = $k;
    }

    public function getKeyboard(): Keyboard
    {
        return $this->keyboard;
    }

}
