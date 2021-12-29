<?php
namespace Lovata\BaseCode\Classes\Helper;

class Receipt
{
    public $emojis = [
        'document' => "\xF0\x9F\x93\x83 "
    ];

    protected $txt = "";

    public function __construct($txt = "")
    {
        $this->txt = $txt;
    }

    /**
     * @param $params $params [
     * @var mixed first_name - Опциональный параметр
     * @var mixed last_name - Опциональный параметр
     * @var mixed email - Опциональный параметр
     * @var mixed phone - Опционаьлный параметр
     * @var mixed address - Опциональный параметр
     * @var mixed comment - Опциональный параметр
     * @var mixed delivery_method_name - Опциональный параметр
     * @var mixed payment_method_name - Опциональный параметр
     * @var mixed change - Опциональный параметр
     * @var mixed spot_name - Опциональный параметр
     * @var mixed total - Опциональный параметр
     * @var mixed products [
     *      @var mixed name
     *      @var mixed count
     * ]
     *
     * ]
     */

    public function make($params):string
    {
        $collection = collect($params);
        $this->param('first_name', $collection->get('first_name'))
            ->param('last_name', $collection->get('last_name'))
            ->param('email', $collection->get('email'))
            ->param('phone', $collection->get('phone'))
            ->param('address', $collection->get('address'))
            ->param('comment', $collection->get('comment'))
            ->param('delivery_method_name', $collection->get('delivery_method_name'))
            ->param('payment_method_name', $collection->get('payment_method_name'))
            ->param('change', $collection->get('change'))
            ->param('spot_name', $collection->get('spot_name'))
            ->newLine()
            ->products($collection->get('products', []))
            ->newLine()
            ->param('total', $collection->get('total'));

        return $this->txt;
    }

    public function products($products): Receipt
    {
        $this->b("Товары в заказе")
            ->colon()
            ->newLine();
        foreach ($products as $product) {
            $this->product($product['name'], $product['count'])
                ->newLine();
        }
        return $this;
    }

    public function product($name, $count): Receipt
    {
        return $this->hyphen()
            ->space()
            ->p($name)
            ->space()
            ->p("x")
            ->p($count);
    }

    public function headline($headline): Receipt
    {
        return $this->emoji($this->emojis['document'])
            ->b($headline)
            ->newLine()
            ->newLine();
    }

    public function param($key, $txt): Receipt
    {
        if (!isset($txt)) {
            return $this;
        }
        if (empty($txt)) {
            return $this;
        }
        return $this->b($this->trans($key))
            ->colon()
            ->space()
            ->p($txt)
            ->newLine();
    }

    public function colon(): Receipt
    {
        return $this->p(":");
    }

    public function space(): Receipt
    {
        return $this->p(" ");
    }

    public function hyphen(): Receipt
    {
        return $this->p("-");
    }

    public function newLine(): Receipt
    {
        return $this->p("\n");
    }

    public function b($txt): Receipt
    {
        return $this->p("<b>" . $txt . "</b>");
    }

    public function emoji($emoji): Receipt
    {
        return $this->p($emoji);
    }

    public function p($txt): Receipt
    {
        if (isset($txt)) {
            $this->txt .= $txt;
        }
        return $this;
    }

    public function getText(): string
    {
        return $this->txt;
    }

    public function trans($key): string
    {
        return \Lang::get('lovata.basecode::lang.receipt.' . $key);
    }
}
?>
