<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Markups\MainMenuReplyMarkup;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use Lovata\BaseCode\Models\Branches;

class ChoseBranchHandler extends CallbackQueryHandler
{
    use Lang;
    use Warn;
    protected $branch;

    public function validate():bool
    {

        $this->branch = Branches::where('id', '=', $this->arguments['id'])->first();

        if (!isset($this->customer)) {
            $this->warn("Customer is not found");
            return false;
        }

        if (!isset($this->branch)) {
            $this->warn("Branch is not found");
            return false;
        }
        return true;
    }
    public function handle()
    {
        $update = $this->getUpdate();
        $chat = $update->getChat();

        $this->customer->branch_id = $this->branch->id;
        $this->customer->save();

        $response = $this->replyWithMessage([
            'chat_id' => $chat->id,
            'text' => 'Вы выбрали заведение: ' . $this->branch->name
        ]);

        $this->telegram->pinChatMessage([
            'chat_id' => $chat->id,
            'message_id' => $response['message_id']
        ]);

        $this->cart->products()->delete();

        $from = $update->getMessage()->getChat();

        $text = sprintf(
            self::lang('start_text'),
            $from->firstName
        );

        $this->replyWithMessage([
            'text' => $text,
            'reply_markup' => MainMenuReplyMarkup::getKeyboard()
        ]);
    }
}
