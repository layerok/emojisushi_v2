<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class ProductInCartReplyMarkup
{
    use Lang;

    protected $keyboard;

    public function __construct()
    {
        $k = new Keyboard();
        $k->inline();
        $k->row($k::inlineButton([
            'text' => $this->lang('position_in_basket'),
            'callback_data' => Constants::NOPE
        ]));
        $this->keyboard = $k;
    }

    public function getKeyboard()
    {
        return $this->keyboard;
    }
}
