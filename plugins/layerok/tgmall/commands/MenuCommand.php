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
    protected $description = "Start Command to get you started";

    /**
     * @inheritdoc
     */
    public function handle()
    {
        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();


        // This will update the chat status to typing...
        //$this->replyWithChatAction(['action' => Actions::TYPING]);
        $keyboard = new Keyboard();
        $keyboard->inline();

        $categories = Category::all();
        $categories->map(function ($row, $idx) use ($keyboard) {
            $callback_data = collect([
                "tag" => Constants::SHOW_PRODUCTS_BY_CATEGORY,
                "category_id" => $row->id,
                "page" => 1
            ]);
            $btn = $keyboard::inlineButton(
                [
                    'text' => $row->name,
                    'callback_data' => $callback_data->toJson()
                ]
            );
            $keyboard->row($btn);
        });


        $this->replyWithMessage([
            'text' => $this->lang('menu_text'),
            'reply_markup' => $keyboard->toJson()
        ]);
    }
}
