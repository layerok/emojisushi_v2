<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Before;
use Layerok\TgMall\Classes\Traits\Lang;


class StartHandler extends CallbackQueryHandler
{
    use Lang;
    use Before;
    public function handle()
    {
        $this->before();
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
