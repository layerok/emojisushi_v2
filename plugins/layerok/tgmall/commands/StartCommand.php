<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Callback\Constants;
use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Layerok\TgMall\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Keyboard\Button;

class StartCommand extends Command
{
    use Lang;

    protected $name = "start";

    /**
     * @var string Command Description
     */
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle()
    {

        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();

        $customer = Customer::where('tg_chat_id', '=', $chat->id)->first();

        if (!$customer) {
            $pass = "qweasdqweasd";
            $user = User::create([
                'name' => "jonh",
                'password' => $pass,
                'password_confirmation' => $pass
            ]);
            Customer::create([
                "tg_chat_id" => $chat->id,
                "firstname" => $from->firstName,
                "lastname"  => $from->lastName,
                "tg_username" => $from->username,
                "user_id" => $user->id
            ]);
        }

        $text = sprintf(
            $this->lang('start_text'),
            $from->username
        );

        $replyMarkup = new MainMenuReplyMarkup();

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $replyMarkup->getKeyboard()
        ]);
    }
}
