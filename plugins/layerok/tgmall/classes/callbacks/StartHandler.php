<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;


class StartHandler extends CallbackQueryHandler
{
    use Lang;

    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckNotChosenBranchMiddleware::class
    ];
    public function handle()
    {
        $update = $this->getUpdate();
        $from = $update->getMessage()->getChat();

        $text = sprintf(
            self::lang('start_text'),
            $from->getFirstName()
        );

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => MainMenuReplyMarkup::getKeyboard()
        ]);
    }
}
