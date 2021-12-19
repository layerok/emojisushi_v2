<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Constants;
use Layerok\Tgmall\Classes\Markups\CartFooterReplyMarkup;
use Layerok\TgMall\Classes\Markups\CategoryFooterReplyMarkup;
use Layerok\TgMall\Commands\LayerokCommand;
use Layerok\TgMall\Classes\Markups\CartProductReplyMarkup;
use Layerok\TgMall\Classes\Markups\ProductInCartReplyMarkup;
use Layerok\TgMall\Models\Message;
use Layerok\TgMall\Traits\Lang;
use Layerok\TgMall\Traits\Warn;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;

class CartCommand extends LayerokCommand
{

    use Warn;
    use Lang;
    protected $name = "cart";
    protected $description = "a command for adding, updating, removing products in the cart";
    protected $pattern = "{type} {product_id?} {quantity?}";
    private $brokenImageFileId = "AgACAgQAAxkDAAIBGGGtGjcxSQraUNasYICGA2UkTLeOAAJyrTEbmQABbVHg3HGg2xXRvQEAAwIAA3gAAyIE";

    public $product;
    public $cart;
    public $types = ['add', 'remove', 'list'];

    /**
     * @var Money
     */
    public $money;
    public $chat;
    public $customer;
    public $user;

    public function validate(): bool
    {
        if (!isset($this->arguments['type'])) {
            $this->warn('You need to provide the type of cart action: ['. implode(
                    ', ',
                    $this->types
                ) . ']'
            );
            return false;
        }
        if (!in_array($this->arguments['type'], $this->types)) {
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
        parent::handle();
        if (!$this->validate()) {
            return;
        }

        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $this->chat = $update->getChat();

        $type = $this->arguments['type'];
        //todo:: it is possible that the customer does not exist with the provided chat id
        $this->customer = Customer::where('tg_chat_id', '=', $this->chat->id)->first();
        $this->user = $this->customer->user;
        $this->cart = Cart::byUser($this->user);
        $this->money = app(Money::class);


        switch ($type) {
            case "add":
                $this->addProduct();
                break;
            case "list":
                $this->listProducts();
                break;
            case "remove":
                $this->removeProduct();
                break;
        }
    }

    public function addProduct()
    {
        $cartProduct = CartProduct::where([
            ['cart_id', '=', $this->cart->id],
            ['product_id', '=', $this->arguments['product_id']]
        ])->first();

        if (isset($cartProduct) && $cartProduct->quantity + $this->arguments['quantity'] < 1) {
            return;
        }

        $k = null;
        $this->cart->addProduct($this->product, $this->arguments['quantity']);
        $this->cart->refresh();

        if (isset($cartProduct)) {
            $cartProduct->refresh();
        }

        $message = Message::where('chat_id', '=', $this->chat->id)
            ->where('type', '=', Constants::UPDATE_CART_TOTAL)
            ->orWhere('type', '=', Constants::UPDATE_CART_TOTAL_IN_CATEGORY)
            ->latest()
            ->first();

        if (!$message) {
            return;
        }

        if ($message->type === Constants::UPDATE_CART_TOTAL) {
            $k = $this->cartFooterKeyboard();
            $totalPrice = $this->money->format(
                $cartProduct->price()->price * $cartProduct->quantity,
                null,
                Currency::$defaultCurrency
            );

            $cartProductReplyMarkup = new CartProductReplyMarkup(
                $cartProduct->product->id,
                $cartProduct->quantity,
                $totalPrice
            );

            \Telegram::editMessageReplyMarkup([
                'chat_id' => $this->chat->id,
                'message_id' => $this->getUpdate()->getMessage()->message_id,
                'reply_markup' => $cartProductReplyMarkup->getKeyboard()
            ]);

            \Telegram::editMessageReplyMarkup([
                'chat_id' => $this->chat->id,
                'message_id' => $message->msg_id,
                'reply_markup' => $k->toJson()
            ]);

        }
        if ($message->type === Constants::UPDATE_CART_TOTAL_IN_CATEGORY) {
            $categoryProductReplyMarkup = new ProductInCartReplyMarkup();
            $k = $this->categoryFooterButtons($message->meta_data);

            \Telegram::editMessageReplyMarkup([
                'chat_id' => $this->chat->id,
                'message_id' => $this->getUpdate()->getMessage()->message_id,
                'reply_markup' => $categoryProductReplyMarkup->getKeyboard()
            ]);

            \Telegram::editMessageReplyMarkup([
                'chat_id' => $this->chat->id,
                'message_id' => $message->msg_id,
                'reply_markup' => $k->toJson()
            ]);
        }
    }

    public function removeProduct()
    {
        $cartProduct = CartProduct::where([
            ['cart_id', '=', $this->cart->id],
            ['product_id', '=', $this->arguments['product_id']]
        ])->first();

        // todo: add check for existence
        $this->cart->removeProduct($cartProduct);
        $this->cart->refresh();

        $message = Message::where([
            ['chat_id', '=', $this->chat->id],
            ['type', '=', Constants::UPDATE_CART_TOTAL]
        ])->first();


        \Telegram::deleteMessage([
            'chat_id' => $this->chat->id,
            'message_id' => $this->getUpdate()->getMessage()->message_id
        ]);


        \Telegram::editMessageText(array_merge(
            $this->cartFooterMessage(),
            [
                'message_id' => $message->msg_id,
                'chat_id' => $this->chat->id
            ]
        ));
    }

    public function listProducts()
    {
        $this->replyWithMessage([
            'text' => $this->lang('busket')
        ]);

        $this->cart->products->map(function ($cartProduct) {


            $id = $cartProduct->product->id;
            $quantity = $cartProduct->quantity;
            $totalPrice = $this->money->format(
                $cartProduct->price()->price * $quantity,
                null,
                Currency::$defaultCurrency
            );

            $cartProductReplyMarkup = new CartProductReplyMarkup($id, $quantity, $totalPrice);
            $k = $cartProductReplyMarkup->getKeyboard();

            if (is_null($cartProduct->product->image)) {
                $photoIdOrUrl = $this->brokenImageFileId;
            } else {
                $photoIdOrUrl = is_null($cartProduct->product->image->file_id) ?
                    \Telegram\Bot\FileUpload\InputFile::create(
                        $cartProduct->product->image->path
                    ) : $cartProduct->product->image->file_id;
            }

            $caption = "<b>" . $cartProduct->name . "</b>\n\n" . \Html::strip($cartProduct->description);
            $response = $this->replyWithPhoto([
                'photo' => $photoIdOrUrl,
                'caption' => $caption,
                'reply_markup' => $k->toJson(),
                'parse_mode' => 'html'
            ]);
        });


        $response = $this->replyWithMessage(
            $this->cartFooterMessage()
        );

        if ($this->cart->products->count() === 0) {
            return;
        }

        $msg_id = $response["message_id"];

        Message::where([
            ['chat_id', '=', $this->chat->id],
            ['type', '=', Constants::UPDATE_CART_TOTAL]
        ])->delete();

        Message::create([
            'chat_id' => $this->chat->id,
            'msg_id' => $msg_id,
            'type' => Constants::UPDATE_CART_TOTAL
        ]);
    }

    public function cartFooterMessage()
    {
        $text = $this->cart->products->count() === 0 ?
            $this->lang('cart_is_empty') :
            $this->lang('rasd');
        return [
            'text' => $text,
            'reply_markup' => $this->cartFooterKeyboard()
        ];
    }

    public function cartFooterKeyboard(): Keyboard
    {
        $replyMarkup = new CartFooterReplyMarkup($this->cart);

        return $replyMarkup->getKeyboard();
    }

    public function categoryFooterButtons($meta_data): Keyboard
    {
        $page = $meta_data['page'];
        $category_id = $meta_data['category_id'];
        $replyMarkup = new CategoryFooterReplyMarkup($this->cart, $category_id, $page);
        return $replyMarkup->getKeyboard();
    }

}
