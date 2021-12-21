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
            $k->row(new ChoseBranchButton($branch));
        });

        Telegram::sendMessage([
            'chat_id' =>  $this->getUpdate()->getChat()->id,
            'text' => $this->lang('chose_branch'),
            'reply_markup' => $k
        ]);
    }
}
