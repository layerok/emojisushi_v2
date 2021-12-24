<?php namespace Layerok\TgMall\Classes\Commands;

use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Telegram\Bot\Commands\Command;

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
        $from = $update->getMessage()->getChat();

        $text = sprintf(
            $this->lang('start_text'),
            $from->firstName
        );

        $replyMarkup = new MainMenuReplyMarkup();

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => $replyMarkup->getKeyboard()
        ]);
    }
}
