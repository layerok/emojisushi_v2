<?php namespace Layerok\TgMall\Classes\Callbacks;


use Layerok\TgMall\Classes\Constants;
use Layerok\Tgmall\Classes\Markups\CartEmptyReplyMarkup;
use Layerok\Tgmall\Classes\Markups\CartFooterReplyMarkup;
use Layerok\TgMall\Classes\Markups\CartProductReplyMarkup;
use Layerok\TgMall\Classes\Markups\CategoryFooterReplyMarkup;
use Layerok\TgMall\Classes\Markups\ProductInCartReplyMarkup;
use Layerok\TgMall\Classes\Utils\PriceUtils;
use Layerok\TgMall\Classes\Utils\Utils;

use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use Lovata\BaseCode\Models\HideProduct;
use OFFLINE\Mall\Classes\Utils\Money;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;

class CartHandler extends CallbackQueryHandler
{
    use Warn;
    use Lang;

    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];

    private $brokenImageFileId = "AgACAgQAAxkDAAIBGGGtGjcxSQraUNasYICGA2UkTLeOAAJyrTEbmQABbVHg3HGg2xXRvQEAAwIAA3gAAyIE";

    public $product;
    public $cart;
    public $types = ['add', 'remove', 'list'];

    /**
     * @var Money
     */
    public $money;
    public $chat;
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

        if (!isset($this->arguments['id'])) {
            $this->warn('You need to provide id of the product to be added to the cart');
            return false;
        }

        $this->product = Product::find($this->arguments['id']);

        if (!$this->product) {
            $this->warn("Can not find the product with the provided id [{$this->arguments['id']}]");
            return false;
        }

        if ($this->arguments['type'] === 'add') {
            if (!isset($this->arguments['qty'])) {
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
        $this->chat = $update->getChat();

        $type = $this->arguments['type'];
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
        $hidden = HideProduct::where([
            ['branch_id', '=', $this->customer->branch->id],
            ['product_id', '=', $this->arguments['id']]
        ])->exists();

        if ($hidden) {
            return;
        }

        $cartProduct = CartProduct::where([
            ['cart_id', '=', $this->cart->id],
            ['product_id', '=', $this->arguments['id']]
        ])->first();

        if (isset($cartProduct) && $cartProduct->quantity + $this->arguments['qty'] < 1) {
            return;
        }

        $k = null;
        $this->cart->addProduct($this->product, $this->arguments['qty']);
        $this->cart->refresh();

        if (isset($cartProduct)) {
            $cartProduct->refresh();
        } else {
            $cartProduct = CartProduct::where([
                ['cart_id', '=', $this->cart->id],
                ['product_id', '=', $this->arguments['id']]
            ])->first();
        }

        $cartTotalMsg = $this->state->getCartTotalMsg();

        if (isset($cartTotalMsg)) {
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

            try {
                \Telegram::editMessageReplyMarkup([
                    'chat_id' => $this->chat->id,
                    'message_id' => $this->getUpdate()->getMessage()->message_id,
                    'reply_markup' => $cartProductReplyMarkup->getKeyboard()
                ]);
            } catch (\Exception $e) {
                \Log::warning("Caught Exception ('{$e->getMessage()}')\n{$e}\n");
            }

            if ($cartTotalMsg['total'] == $this->cart->totals()->totalPostTaxes()) {
                // Общая стоимость товаров в корзине совпадает с тем что написано в сообщении
                return;
            }

            try {
                \Telegram::editMessageReplyMarkup([
                    'chat_id' => $this->chat->id,
                    'message_id' => $cartTotalMsg['id'],
                    'reply_markup' => $k->toJson()
                ]);
            } catch (\Exception $e) {
                \Log::warning("Caught Exception ('{$e->getMessage()}')\n{$e}\n");
            }
        }
    }

    public function removeProduct()
    {
        $cartProduct = CartProduct::where([
            ['cart_id', '=', $this->cart->id],
            ['product_id', '=', $this->arguments['id']]
        ])->first();

        // todo: add check for existence
        $this->cart->removeProduct($cartProduct);
        $this->cart->refresh();

        try {
            \Telegram::deleteMessage([
                'chat_id' => $this->chat->id,
                'message_id' => $this->getUpdate()->getMessage()->message_id
            ]);
        } catch (\Exception $e) {
            \Log::warning("Caught Exception ('{$e->getMessage()}')\n{$e}\n");
        }

        $cartTotalMsg = $this->state->getCartTotalMsg();

        if (isset($cartTotalMsg)) {
            if ($cartTotalMsg['total'] == $this->cart->totals()->totalPostTaxes()) {
                // Общая стоимость товаров в корзине совпадает с тем что написано в сообщении
                return;
            }
            try {
                \Telegram::editMessageText(array_merge(
                    $this->cartFooterMessage(),
                    [
                        'message_id' => $cartTotalMsg['id'],
                        'chat_id' => $this->chat->id
                    ]
                ));
            } catch (\Exception $e) {
                \Log::warning("Caught Exception ('{$e->getMessage()}')\n{$e}\n");
            }
        }
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

            $caption = Utils::getCaption($cartProduct->product);

            if (!is_null($cartProduct->product->image)) {
                $photoIdOrUrl = Utils::getPhotoIdOrUrl($cartProduct->product);
                $response = $this->replyWithPhoto([
                    'photo' => $photoIdOrUrl,
                    'caption' => $caption,
                    'reply_markup' => $k->toJson(),
                    'parse_mode' => 'html',
                ]);

                Utils::setFileIdFromResponse($response, $cartProduct->product);
            } else {
                $this->replyWithMessage([
                    'text' => $caption,
                    'reply_markup' => $k->toJson(),
                    'parse_mode' => 'html',
                ]);
            }
        });


        $response = $this->replyWithMessage(
            $this->cartFooterMessage()
        );

        if ($this->cart->products->count() === 0) {
            return;
        }

        $msg_id = $response["message_id"];

        $this->state->setCartTotalMsg(
            [
                'id' => $msg_id,
                'total' => $this->cart->totals()->totalPostTaxes()
            ]
        );
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
        if ($this->cart->products->count() === 0) {
            $replyMarkup = new CartEmptyReplyMarkup();
        } else {
            $replyMarkup = new CartFooterReplyMarkup($this->cart);
        }

        return $replyMarkup->getKeyboard();
    }


}
