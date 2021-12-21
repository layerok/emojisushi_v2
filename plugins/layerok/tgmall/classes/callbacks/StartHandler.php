<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;


class StartHandler extends CallbackQueryHandler
{
    use Lang;

    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];
    public function handle()
    {
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
