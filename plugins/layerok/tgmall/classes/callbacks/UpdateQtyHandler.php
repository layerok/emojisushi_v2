<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Markups\CategoryProductReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use OFFLINE\Mall\Models\Product;

class UpdateQtyHandler extends CallbackQueryHandler
{
    use Lang;
    use Warn;

    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];

    public $product;


    public function validate(): bool
    {
        if (!isset($this->arguments['id'])) {
            $this->warn("To update product quantity you need to provide product_id");
            return false;
        }

        if (!isset($this->arguments['qty'])) {
            $this->warn("To update product quantity you need to provide quantity");
            return false;
        }

        if ($this->arguments['qty'] < 1) {
            $this->warn("Quantity of product can not be less than 1");
            return false;
        }

        $this->product = Product::find($this->arguments['id']);
        if (!$this->product) {
            $this->warn("Trying to update quantity of product that does not exist [{$this->arguments['id']}]");
            return false;
        }
        return true;
    }
    /**
     * @inheritdoc
     */
    public function handle()
    {
        if (!$this->validate()) {
            return;
        }
        $quantity = $this->arguments['qty'];

        $update = $this->getUpdate();
        $chat = $update->getChat();
        $message = $update->getMessage();

        $replyMarkup = new CategoryProductReplyMarkup($this->product, $quantity);

        // Очень интересный момент, еслу у какой-то кнопки callback_data будет отсутствовать
        // или будет пустой строкой, такая клавиатура не обновится
        \Telegram::editMessageReplyMarkup([
            'chat_id' => $chat->id,
            'message_id' => $message->message_id,
            'reply_markup' => $replyMarkup->getKeyboard()
        ]);
    }
}
