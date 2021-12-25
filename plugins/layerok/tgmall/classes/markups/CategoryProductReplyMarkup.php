<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
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
     * @param $quantity
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

/*        $btn1 = $k::inlineButton([
            'text' => $this->lang('minus'),
            'callback_data' => json_encode([
                'name' => ($quantity - 1) < 1 ? 'noop': 'update_qty',
                'arguments' => [
                    'id' => $product['id'],
                    'qty'   => $quantity - 1
                ]
            ])
        ]);
        $btn2 = $k::inlineButton([
            'text' => $quantity,
            'callback_data' => json_encode([
                'name' => 'noop'
            ])
        ]);
        $btn3 = $k::inlineButton([
            'text' => $this->lang('plus'),
            'callback_data' => json_encode([
                'name' =>  'update_qty',
                'arguments' => [
                    'id' => $product['id'],
                    'qty'   => $quantity + 1
                ]
            ])
        ]);
        $k->row($btn1, $btn2, $btn3);*/

        $k->row($k::inlineButton([
            'text' => $this->lang('price') . ": " . $totalPrice,
            'callback_data' => json_encode([
                'name' => 'noop'
            ])
        ]));
        $k->row($k::inlineButton([
            'text' => $this->lang('add_to_cart'),
            'callback_data' => json_encode([
                'name' => 'add_product',
                'arguments' => [
                    'id' => $product['id'],
                    'qty' =>  $quantity
                ]
            ])
        ]));


        $this->keyboard = $k;
    }

    public function getKeyboard(): Keyboard
    {
        return $this->keyboard;
    }

}
