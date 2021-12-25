<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Buttons\ChoseBranchButton;
use Layerok\TgMall\Classes\Traits\Lang;
use Lovata\BaseCode\Models\Branches;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class ListBranchesHandler extends CallbackQueryHandler
{
    use Lang;
    public function handle()
    {
        $branches = Branches::all();
        $k = new Keyboard();
        $k->inline();
        $branches->map(function ($branch) use ($k) {
            $btn = new ChoseBranchButton($branch);
            $k->row($k::inlineButton($btn->getData()));
        });

        Telegram::sendMessage([
            'chat_id' =>  $this->getUpdate()->getChat()->id,
            'text' => self::lang('chose_branch'),
            'reply_markup' => $k
        ]);
    }
}
