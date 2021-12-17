<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Callback\Constants;
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

        $keyboard = new Keyboard();
        $keyboard->inline();

        $row1 = [];
        $row2 = [];
        $row3 = [];

        $row1[] = $keyboard::inlineButton([
            'text' => $this->lang('menu'),
            'callback_data' => "/menu"
        ]);
        $row1[] = $keyboard::inlineButton([
            'text' => $this->lang('busket'),
            'callback_data' => "/cart list"
        ]);

        $row2[] = $keyboard::inlineButton([
                'text' => $this->lang('delivery_and_pay'),
                'callback_data' => "delivery_and_pay"
        ]);
        $row2[] = $keyboard::inlineButton([
                'text' => $this->lang('my_order'),
                'callback_data' => "my_order"
        ]);

        $row3[] = $keyboard::inlineButton([
                'text' => $this->lang('review'),
                'callback_data' => "review"
        ]);
        $row3[] = $keyboard::inlineButton([
            'text' => $this->lang('contact'),
            'callback_data' => "contact"
        ]);


        $keyboard->row(...$row1);
        $keyboard->row(...$row2);
        $keyboard->row(...$row3);

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $keyboard->toJson()
        ]);
    }
}
