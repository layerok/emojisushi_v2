<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Traits\Lang;
use Layerok\TgMall\Traits\Warn;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class CartCommand extends Command {

    use Warn;
    use Lang;
    protected $name = "cart";
    protected $description = "a command for adding, updating, removing products in the cart";
    protected $pattern = "{type} {product_id?} {quantity?}";
    private $brokenImageFileId = "AgACAgQAAxkDAAIBGGGtGjcxSQraUNasYICGA2UkTLeOAAJyrTEbmQABbVHg3HGg2xXRvQEAAwIAA3gAAyIE";

    public $product;
    public $cart;
    public $types = ['add', 'remove', 'list'];

    public function validate(): bool
    {
        if (!isset($this->arguments['type'])) {
            $this->warn('You need to provide the type of cart action: [add, remove, list]');
            return false;
        }
        if (!in_array($this->arguments['type'], $this->types))
        {
            $this->warn(
                'Unrecognized cart command type, you can only use on of this types: ['. implode(
                    ', ',
                    $this->types
                ) . ']'
            );
            return false;
        }

        if ($this->arguments['type'] == 'list') {
            return true;
        }

        if (!isset($this->arguments['product_id'])) {
            $this->warn('You need to provide id of the product to be added to the cart');
            return false;
        }

        $this->product = Product::find($this->arguments['product_id']);

        if (!$this->product) {
            $this->warn("Can not find the product with the provided id [{$this->arguments['product_id']}]");
            return false;
        }

        if ($this->arguments['type'] === 'add') {
            if (!isset($this->arguments['quantity'])) {
                $this->warn("To add or update product in the cart you need to specify quantity");
                return false;
            }
        }

        return true;
    }

    public function handle()
    {
        if (!$this->validate()) {
            return;
        }

        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();

        $type = $this->arguments['type'];
        //todo:: it is possible that the customer does not exist with the provided chat id
        $customer = Customer::where('tg_chat_id', '=', $chat->id)->first();
        $user = $customer->user;
        $this->cart = Cart::byUser($user);


        switch ($type) {
            case "add":
                $this->cart->addProduct($this->product, $this->arguments['quantity']);
                break;
            case "list":
                $this->listProducts();
                break;
            case "remove":
                $this->cart->removeProduct($this->product);
                break;
        }
    }

    public function listProducts()
    {
        $this->replyWithMessage([
            'text' => $this->lang('busket')
        ]);

        $money = app(Money::class);
        $defaultCurrency = Currency::$defaultCurrency;

        $this->cart->products->map(function ($product) use ($money, $defaultCurrency) {
            $k = new Keyboard();
            $k->inline();

            $btn1 = $k::inlineButton([
                'text' => $this->lang('minus'),
                'callback_data' => implode(' ', ["/cart", "add", $product->id, ($product->quantity - 1)])
            ]);

            $btn2 = $k::inlineButton([
                'text' => $product->quantity. "/10",
                'callback_data' => 'do nothing'
            ]);

            $btn3 = $k::inlineButton([
                'text' => $this->lang('plus'),
                'callback_data' =>  implode(["/cart", "add", $product->id, ($product->quantity + 1)])
            ]);

            $btn4 = $k::inlineButton([
                'text' => $this->lang('del'),
                'callback_data' =>  "/cart remove {$product->id}"
            ]);


            $k->row($btn1, $btn2, $btn3, $btn4);

            $totalPrice = $money->format($product->price()->price * $product->quantity, null, $defaultCurrency);
            $k->row($k::inlineButton([
                'text' => str_replace(
                    "*price*",
                    $totalPrice,
                    $this->lang('prise_position_basket')
                ),
                'callback_data' => "do nothing"
            ]));

            if (is_null($product->product->image)) {
                $photoIdOrUrl = $this->brokenImageFileId;
            } else {
                $photoIdOrUrl = is_null($product->product->image->file_id) ?
                    \Telegram\Bot\FileUpload\InputFile::create(
                        $product->product->image->path
                    ) : $product->product->image->file_id;
            }

            $caption = "<b>" . $product->name . "</b>\n\n" . \Html::strip($product->description);
            $response = $this->replyWithPhoto([
                'photo' => $photoIdOrUrl,
                'caption' => $caption,
                'reply_markup' => $k->toJson(),
                'parse_mode' => 'html'
            ]);
        });

        if ($this->cart->products->count() === 0) {
            $this->replyWithMessage([
                'text' => $this->lang('busket_is_empty')
            ]);
        } else {
            $k = new Keyboard();
            $k->inline();


            $k->row($k::inlineButton([
                'text' => str_replace(
                    "*price*",
                    $money->format(
                        $this->cart->totals()->totalPostTaxes(),
                        null,
                        $defaultCurrency
                    ),
                    $this->lang('all_amount_order')
                ),
                'callback_data' => 'do nothing'
            ]));

            $k->row($k::inlineButton(([
                'text' => $this->lang('take_order'),
                'callback_data' => 'take_order'
            ])));

            $k->row($k::inlineButton([
                'text' => $this->lang('in_menu_main'),
                'callback_data' => '/start'
            ]));

            $this->replyWithMessage([
                'text' => $this->lang('rasd'),
                'reply_markup' => $k->toJson()
            ]);
        }
    }


}
