<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Messages\OrderCommentHandler;

class LeaveCommentHandler extends CallbackQueryHandler
{
    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];
    public function handle()
    {
        // самовывоз
        \Telegram::sendMessage([
            'text' => 'Комментарий к заказу',
            'chat_id' => $this->update->getChat()->id,
        ]);
        $this->state->setMessageHandler(OrderCommentHandler::class);
    }
}
