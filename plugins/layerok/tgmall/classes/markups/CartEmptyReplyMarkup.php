<?php namespace Layerok\Tgmall\Classes\Markups;

use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Classes\Utils\Money;
use Telegram\Bot\Keyboard\Keyboard;

class CartEmptyReplyMarkup
{
    use Lang;
    protected $keyboard;
    /**
     * @var Money
     */
    protected $money;

    public function __construct()
    {
        $this->money = app(Money::class);
        $k = new Keyboard();
        $k->inline();

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
