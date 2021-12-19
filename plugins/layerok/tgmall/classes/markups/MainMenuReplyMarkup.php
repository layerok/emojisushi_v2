<?php namespace Layerok\TgMall\Classes\Markups;

use Layerok\TgMall\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class MainMenuReplyMarkup
{
    use Lang;

    protected $keyboard;

    public function __construct()
    {
        $keyboard = new Keyboard();
        $keyboard->inline();

        $row1 = [];
        $row2 = [];
        $row3 = [];

        $row1[] = $keyboard::inlineButton([
            'text' => $this->lang('menu'),
            'callback_data' => "/menu"
        ]);
        $row1[] = $keyboard::inlineButton([
            'text' => $this->lang('busket'),
            'callback_data' => "/cart list"
        ]);

        $row2[] = $keyboard::inlineButton([
            'text' => $this->lang('delivery_and_pay'),
            'callback_data' => "delivery_and_pay"
        ]);
        $row2[] = $keyboard::inlineButton([
            'text' => $this->lang('my_order'),
            'callback_data' => "my_order"
        ]);

        $row3[] = $keyboard::inlineButton([
            'text' => $this->lang('review'),
            'callback_data' => "review"
        ]);
        $row3[] = $keyboard::inlineButton([
            'text' => $this->lang('contact'),
            'callback_data' => "contact"
        ]);


        $keyboard->row(...$row1);
        $keyboard->row(...$row2);
        $keyboard->row(...$row3);

        $this->keyboard = $keyboard;
    }

    public function getKeyboard(): Keyboard
    {
        return $this->keyboard;
    }
}
