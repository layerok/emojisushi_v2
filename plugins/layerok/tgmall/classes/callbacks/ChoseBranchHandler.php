<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Lovata\BaseCode\Models\Branches;

class ChoseBranchHandler extends CallbackQueryHandler
{
    use Lang;
    public function handle()
    {
        $update = $this->getUpdate();
        $chat = $update->getChat();

        $branch = Branches::where('id', '=', $this->arguments['id'])->first();

        if (!isset($branch)) {
            $this->warn("You provided non existent restaurant id");
            return;
        }


        if (!isset($this->customer)) {
            // customer must be created on this stage
            \Log::error('somehow user was not created');
            return;
        }

        $this->customer->branch_id = $branch->id;
        $this->customer->save();

        $response = $this->replyWithMessage([
            'chat_id' => $chat->id,
            'text' => 'Вы выбрали заведение: ' . $branch->name
        ]);

        \Telegram::pinChatMessage([
            'chat_id' => $chat->id,
            'message_id' => $response['message_id']
        ]);

        $this->cart->products()->delete();

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
