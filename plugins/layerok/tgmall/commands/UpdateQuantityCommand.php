<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Callback\Constants;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Layerok\TgMall\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Keyboard\Button;

class UpdateQuantityCommand extends Command
{
    use Lang;

    protected $name = "update_qty";

    protected $pattern = "{quantity}";

    /**
     * @var string Command Description
     */
    protected $description = "Update product quantity";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        if(env('TERMINATE_TELEGRAM_COMMANDS')) {
            return;
        };

        $quantity = $this->arguments['quantity'];
        //$positionId = $callback->position_id;
        if ($quantity <= 1) {
            $quantity = 1;
        }
        if ($quantity >= 10) {
            $quantity = 10;
        }

        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();
        $message = $update->getMessage();
        $replyMarkup = $message->replyMarkup;

        // здесь должна быть проверка, если товар в корзине, то ничего не делаем
        $k = new Keyboard();
        $k->inline();
        $btn1 = $k::inlineButton([
            'text' => $this->lang('minus'),
            'callback_data' => '/update_qty ' . ($quantity - 1)
        ]);
        $btn2 = $k::inlineButton([
            'text' => $quantity . '/10'
        ]);
        $btn3 = $k::inlineButton([
            'text' => $this->lang('plus'),
            'callback_data' => '/update_qty ' . ($quantity + 1)
        ]);
        $k->row($btn1, $btn2, $btn3);

        $btn4 = $k::inlineButton([
            'text' => str_replace("*price*", $quantity, $this->lang("in_basket_button_title")),
        ]);

        $k->row($btn4);

        \Log::info('message_id');
        \Log::info($message->message_id);

        \Telegram::deleteMessage([
            'chat_id' => $chat->id,
            'message_id' => $message->message_id,
        ]);

   /*     \Telegram::editMessageReplyMarkup([
            'chat_id' => $chat->id,
            'message_id' => $message->message_id,
            'reply_markup' => $k->toJson()
        ]);*/
    }
}
