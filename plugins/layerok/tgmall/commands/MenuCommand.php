<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Callback\Constants;
use Layerok\TgMall\Classes\InlineKeyboard;

use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Layerok\TgMall\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Keyboard\Button;

class MenuCommand extends Command
{
    use Lang;

    protected $name = "menu";

    /**
     * @var string Command Description
     */
    protected $description = "Menu Command, Get a list of categories";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        if(env('TERMINATE_TELEGRAM_COMMANDS')) {
            return;
        };
        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();


        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $keyboard = new Keyboard();
        $keyboard->inline();

        $categories = Category::where('nest_depth', '=', 0)->get();
        $categories->map(function ($row) use ($keyboard) {
            $btn = $keyboard::inlineButton(
                [
                    'text' => $row->name,
                    'callback_data' => "/category " . $row->id
                ]
            );
            $keyboard->row($btn);
        });

        $keyboard->row($keyboard::inlineButton([
            'text' => $this->lang('in_menu_main'),
            'callback_data' => "/start"
        ]));

        $replyWith = [
            'text' => $this->lang('menu_text'),
            'reply_markup' => $keyboard->toJson()
        ];

        $this->replyWithMessage($replyWith);

    }
}
