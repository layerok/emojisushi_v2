<?php
namespace Lovata\BaseCode\Classes;
//** Работает только на сервере https */

class Telegram
{
    public $token;
    public $chat_id;
    public $headline;
    public $data;

    public $translates = [
        'first_name' => 'Имя',
        'firstname' => 'Имя',
        'last_name' => 'Фамилия',
        'lastname' => 'Фамилия',
        'phone' => 'Тел',
        'email' => 'Почта',
        'activeLocale' => 'Язык сайта',
        'comment' => 'Комментарий',
        'address' => 'Адрес',
        'products' => 'Товары',
        'total'   => 'Итого',
        'delivery'   => 'Доставка',
        'sticks'   => 'Кол-во палочек',
        'change'   => 'Приготовить сдачу с',
        'payment'   => 'Оплата',
        'spot'      => 'Заведение',
        'payment_status'   => 'Статус оплаты',
    ];
    public $emojis = [
//        'first_name' => "\xE2\x9C\x8F",
//        'last_name'  => "\xE2\x9C\x92",
//        'phone' => "\xF0\x9F\x93\xB1",
//        'email' => "\xF0\x9F\x93\xA7",
//        'comment' => "\xF0\x9F\x93\x9D",
//        'address' => "\xF0\x9F\x93\xA6",
//        'products' => "\xF0\x9F\x8D\xA3",
//        'total'   => "\xF0\x9F\x92\xB5",
//        'drink'   => "\xF0\x9F\x8D\xB9",
//        'pizza'  => "\xF0\x9F\x8D\x95",
//        'spaghetti'   => "\xF0\x9F\x8D\x9C",
//        'snacks' => "\xF0\x9F\x8D\xA4"
    ];



    public function getFormattedMessage($headline, $data):string
    {
        $this->headline = $headline;
        $this->data = $data;
        // send message
        $txt="\xF0\x9F\x93\x83 <b>$this->headline</b> \n\n";
        $txt .= $this->key_value_list($data);

        return $txt;
    }



    public function key_value_list($arr = []): string
    {
        $txt = '';
        foreach ($arr as $key => $value) {
            if (is_array($value)) {
                if ($key !== 'products') {
                    $txt .= $this->key_value_list($value);
                }else {
                    $txt .= $this->product_line($value);
                }
            } else {
                if (!empty(trim($value))) {
                    $emoji = $this->emojis[$key] ?? "";
                    $translate = $this->translates[$key] ?? $key;

                    $txt .= $emoji . " <b>" . $translate . "</b>: " . $value . ".\n";
                }

            }

        }
        return $txt;
    }
    public function product_line($products):string
    {
        $txt = "\n<b>Товары в заказе</b> \n";
        $emoji = $this->emojis['products'] ?? '';
        foreach ($products as $product) {
            $txt .= $emoji . " - " . $product['name'] . " x" . $product['count'] . ".\n";
        }
        $txt .= "\n\n";
        return $txt;
    }
}
?>
