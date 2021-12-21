<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Buttons\ChoseBranchButton;
use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Warn;
use Lovata\BaseCode\Models\Branches;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class BranchInfoHandler extends CallbackQueryHandler
{
    use Warn;

    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];

    protected $types = ["phones", "delivery", "website"];

    protected function validate(): bool
    {
        if (!in_array($this->arguments['type'], $this->types)) {
            \Log::error('invalid type');
            return false;
        }
        return true;
    }

    public function handle()
    {
        if (!$this->validate()) {
            return;
        }

        $method = $this->arguments['type'];

        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function phones()
    {
        $branch = $this->customer->branch;

        $phones = explode(',', $branch->phones);
        foreach ($phones as $phone) {
            $this->replyWithMessage([
                'text' => trim($phone)
            ]);
        }
    }

    protected function delivery()
    {

    }

    protected function website()
    {
        $this->replyWithMessage([
            'text' => 'https://emojisushi.com.ua'
        ]);
    }

}
