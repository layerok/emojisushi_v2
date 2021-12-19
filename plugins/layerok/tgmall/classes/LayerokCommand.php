<?php namespace Layerok\TgMall\Classes;

use Lovata\BaseCode\Models\Branches;
use OFFLINE\Mall\Models\Customer;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class LayerokCommand extends Command
{

    public function isSpotChosen(): bool
    {
        $update = $this->getUpdate();
        $chat = $update->getChat();

        $customer = Customer::where('tg_chat_id', '=', $chat->id)->first();

        $branch = $customer->branch;
        if (!isset($branch)) {
            return false;
        }
        return true;
    }

    public function handle()
    {

        if (!$this->isSpotChosen()) {
            $update = $this->getUpdate();
            $chat = $update->getChat();
            $branches = Branches::all();
            $k = new Keyboard();
            $k->inline();
            $branches->map(function ($branch) use ($k) {
                $k->row($k::inlineButton([
                    'text' => $branch->name,
                    'callback_data' => '/branch ' . $branch->id
                ]));
            });
            Telegram::sendMessage([
                'chat_id' => $chat->id,
                'text' => 'Выберите заведение',
                'reply_markup' => $k
            ]);
            exit;
        }
    }
}
