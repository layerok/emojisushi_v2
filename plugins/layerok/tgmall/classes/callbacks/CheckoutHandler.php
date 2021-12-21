<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;

class CheckoutHandler extends CallbackQueryHandler
{
    use Lang;

    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];

    public function handle()
    {

        $this->replyWithMessage([
            'text' => 'Введите Ваш телефон'
        ]);
        $this->state->setStep(Constants::STEP_PHONE);
    }
}
