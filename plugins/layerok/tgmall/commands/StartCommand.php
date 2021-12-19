<?php namespace Layerok\TgMall\Commands;


use Layerok\TgMall\Classes\LayerokCommand;
use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Actions;
use Layerok\TgMall\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Keyboard\Button;

class StartCommand extends LayerokCommand
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
        parent::handle();
        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();

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
