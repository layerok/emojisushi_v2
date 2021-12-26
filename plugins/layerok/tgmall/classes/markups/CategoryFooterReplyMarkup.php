<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Models\Settings;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Category;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryFooterReplyMarkup
{
    use Lang;

    public static function getKeyboard(Cart $cart, $category_id, $page = 1)
    {
        $cart->refresh();
        $countPositionInOrder = "";
        if ($cart->products->count()) {
            $countPositionInOrder = " (" . $cart->products->count() . ")";
        }
        $limit = Settings::get('products_per_page', 10);
        $all = Category::where('id', '=', $category_id)->first()->products;
        $lastPage = ceil($all->count() / $limit);
        $k = new Keyboard();
        $k->inline();
        if ($lastPage > $page) {
            $loadBtn = $k::inlineButton([
                'text' => 'Загрузить еще из этой категории',
                'callback_data' => json_encode([
                    'name' => 'category',
                    'arguments' => [
                        'id' => $category_id,
                        'page' => $page + 1
                    ]
                ])
            ]);
            $k->row($loadBtn);
        }

        $btn1 = $k::inlineButton([
            'text' => self::lang("busket") . $countPositionInOrder,
            'callback_data' => json_encode([
                'name' => 'cart',
                'arguments' => [
                    'type' => 'list'
                ]
            ])
        ]);
        $btn2 = $k::inlineButton([
            'text' => self::lang("in_menu"),
            'callback_data' => json_encode([
                'name' => 'menu'
            ])
        ]);
        $btn3 = $k::inlineButton([
            'text' => self::lang("in_menu_main"),
            'callback_data' => json_encode([
                'name' => 'start',
                'arguments' => []
            ])
        ]);

        $k->row($btn1);
        $k->row($btn2);
        $k->row($btn3);
        return $k;
    }
}
