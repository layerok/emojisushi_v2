<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\LeaveCommentReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;

class CommentDialogHandler extends CallbackQueryHandler
{
    use Lang;
    public function handle()
    {
        $this->telegram->sendMessage([
            'text' => self::lang('leave_comment_question'),
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => LeaveCommentReplyMarkup::getKeyboard()
        ]);
    }
}
