<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Commands\LayerokCommand;
use Layerok\TgMall\Classes\Markups\CategoryProductReplyMarkup;
use OFFLINE\Mall\Models\Product;
use Layerok\TgMall\Traits\Lang;
use Layerok\TgMall\Traits\Warn;

class UpdateQuantityCommand extends LayerokCommand
{
    use Lang;
    use Warn;

    protected $name = "update_qty";

    protected $pattern = "{product_id} {quantity}";

    public $product;

    public $limit = 10;
    /**
     * @var string Command Description
     */
    protected $description = "Update product quantity";

    public function validate()
    {
        if (!isset($this->arguments['product_id'])) {
            $this->warn("To update product quantity you need to provide product_id");
            return false;
        }

        if (!isset($this->arguments['quantity'])) {
            $this->warn("To update product quantity you need to provide quantity");
            return false;
        }

        if ($this->arguments['quantity'] > $this->limit || $this->arguments['quantity'] < 1) {
            $this->warn("Quantity of product can not be more than {$this->limit}");
            return false;
        }

        $this->product = Product::find($this->arguments['product_id']);
        if (!$this->product) {
            $this->warn("Trying to update quantity of product that does not exist [{$this->arguments['product_id']}]");
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
        parent::before();
        $quantity = $this->arguments['quantity'];

        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();
        $message = $update->getMessage();
        $replyMarkup = $message->replyMarkup;

        if ($replyMarkup['inline_keyboard'][1][0]['callback_data'] == Constants::NOPE) {
            return;
        }

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
