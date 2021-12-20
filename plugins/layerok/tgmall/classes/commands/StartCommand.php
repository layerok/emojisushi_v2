<?php namespace Layerok\TgMall\Classes\Commands;

use Layerok\TgMall\Classes\Commands\LayerokCommand;
use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;

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
        parent::before();
        $update = $this->getUpdate();
        $from = $update->getMessage()->getChat();

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
