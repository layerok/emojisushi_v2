<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\CartProductReplyMarkup;
use Layerok\TgMall\Classes\Markups\CategoryFooterReplyMarkup;
use Layerok\TgMall\Classes\Markups\ProductInCartReplyMarkup;
use Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use Lovata\BaseCode\Models\HideProduct;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Currency;
use OFFLINE\Mall\Models\Product;
use Telegram\Bot\Keyboard\Keyboard;

class AddProductHandler extends CallbackQueryHandler
{
    use Lang;
    use Warn;

    protected $chat;

    /** @var Product */
    protected $product;

    protected $extendMiddlewares = [
        CheckBranchMiddleware::class
    ];

    public function validate(): bool
    {

        if (!isset($this->arguments['id'])) {
            $this->warn('You need to provide id of the product to be added to the cart');
            return false;
        }

        $this->product = Product::find($this->arguments['id']);

        if (!$this->product) {
            $this->warn("Can not find the product with the provided id [{$this->arguments['id']}]");
            return false;
        }

        if (!isset($this->arguments['qty'])) {
            $this->warn("To add product in the cart you need to specify quantity");
            return false;
        }

        return true;
    }

    public function handle()
    {
        $this->chat = $this->update->getChat();

        if (!$this->validate()) {
            return;
        }

        $hidden = HideProduct::where([
            ['branch_id', '=', $this->customer->branch->id],
            ['product_id', '=', $this->arguments['id']]
        ])->exists();

        if ($hidden) {
            return;
        }

        $this->cart->addProduct($this->product, $this->arguments['qty']);
        $this->cart->refresh();

        $cartCountMsg = $this->state->getCartCountMsg();




        if ($cartCountMsg) {
            $categoryProductReplyMarkup = new ProductInCartReplyMarkup();


            try {
                \Telegram::editMessageReplyMarkup([
                    'chat_id' => $this->chat->id,
                    'message_id' => $this->getUpdate()->getMessage()->message_id,
                    'reply_markup' => $categoryProductReplyMarkup->getKeyboard()
                ]);
            } catch (\Exception $e) {
                \Log::warning("Caught Exception ('{$e->getMessage()}')\n{$e}\n");
            }


            if ($cartCountMsg['count'] == $this->cart->products->count()) {
                // Кол-во товаров в корзине совпадает с тем, что написано в сообщении
                return;
            }

            $k = $this->categoryFooterButtons(
                $cartCountMsg['page'],
                $cartCountMsg['category_id']
            );

            try {
                \Telegram::editMessageReplyMarkup([
                    'chat_id' => $this->chat->id,
                    'message_id' => $cartCountMsg['id'],
                    'reply_markup' => $k->toJson()
                ]);
            } catch (\Exception $e) {
                \Log::warning("Caught Exception ('{$e->getMessage()}')\n{$e}\n");
            }
        }
    }

    public function categoryFooterButtons($page, $category_id): Keyboard
    {
        $replyMarkup = new CategoryFooterReplyMarkup($this->cart, $category_id, $page);
        return $replyMarkup->getKeyboard();
    }
}
