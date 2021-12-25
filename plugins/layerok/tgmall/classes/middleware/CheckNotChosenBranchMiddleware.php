<?php namespace Layerok\TgMall\Classes\Middleware;

use Layerok\TgMall\Classes\Buttons\ChoseBranchButton;
use Lovata\BaseCode\Models\Branches;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class CheckNotChosenBranchMiddleware extends AbstractMiddleware
{

    public function isSpotChosen(): bool
    {
        if (!isset($this->customer->branch)) {
            return false;
        }
        return true;
    }

    public function run(): bool
    {
        if (!$this->isSpotChosen()) {
            return false;
        }
        return true;
    }

    public function onFailed():void
    {
        $branches = Branches::all();
        $k = new Keyboard();
        $k->inline();
        $branches->map(function ($branch) use ($k) {
            $btn = new ChoseBranchButton($branch);
            $k->row($k::inlineButton($btn->getData()));
        });

        Telegram::sendMessage([
            'chat_id' =>  $this->update->getChat()->id,
            'text' => 'Выберите заведение',
            'reply_markup' => $k
        ]);
    }


}
