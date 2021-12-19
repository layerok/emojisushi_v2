<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Traits\Warn;
use Lovata\BaseCode\Models\Branches;
use OFFLINE\Mall\Models\Customer;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class ListBranchCommand extends LayerokCommand
{
    use Warn;
    protected $name = "listbranch";
    protected $description = "Use this command to list restaurant location";

    protected function validate(): bool
    {
        return true;
    }

    public function handle()
    {
        if (!$this->validate()) {
            return;
        }
        parent::before(false);

        $this->listBranches();
    }

    public function listBranches()
    {
        $update = $this->getUpdate();
        $chat = $update->getChat();
        $branches = Branches::all();
        $k = new Keyboard();
        $k->inline();
        $branches->map(function ($branch) use ($k) {
            $k->row($k::inlineButton([
                'text' => $branch->name,
                'callback_data' => '/chosebranch ' . $branch->id
            ]));
        });

        Telegram::sendMessage([
            'chat_id' => $chat->id,
            'text' => 'Выберите заведение',
            'reply_markup' => $k
        ]);
    }

}
