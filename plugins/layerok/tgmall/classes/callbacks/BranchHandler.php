<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Traits\Before;
use Layerok\TgMall\Classes\Traits\Warn;
use Lovata\BaseCode\Models\Branches;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

class BranchHandler extends CallbackQueryHandler
{
    use Warn;
    use Before;
    protected $types = ["phones", "delivery", "website", "all", "chose"];

    protected function validate(): bool
    {
        if (!in_array($this->arguments['type'], $this->types)) {
            $this->warn(
                "[type] argument is required for [/branch] command. " .
                "Provide one of this types: " . implode(', ', $this->types) .
                " as a first argument"
            );
            return false;
        }
        return true;
    }

    public function handle()
    {
        if (!$this->validate()) {
            return;
        }
        $this->before();

        $method = $this->arguments['type'];
        if (method_exists($this, $method)) {
            $this->$method();
        }
    }

    protected function phones()
    {
        $branch = $this->customer->branch;

        $phones = explode(',', $branch->phones);
        foreach ($phones as $phone) {
            $this->replyWithMessage([
                'text' => trim($phone)
            ]);
        }
    }

    protected function delivery()
    {

    }

    protected function website()
    {
        $this->replyWithMessage([
            'text' => 'https://emojisushi.com.ua'
        ]);
    }

    public function all()
    {
        $branches = Branches::all();
        $k = new Keyboard();
        $k->inline();
        $branches->map(function ($branch) use ($k) {
            $k->row($k::inlineButton([
                'text' => $branch->name,
                'callback_data' => json_encode([
                    'name' => 'branch',
                    'arguments' => [
                        'type' => 'chose',
                        'id' => $branch->id
                    ]
                ])
            ]));
        });

        Telegram::sendMessage([
            'chat_id' =>  $this->getUpdate()->getChat()->id,
            'text' => 'Выберите заведение',
            'reply_markup' => $k
        ]);
    }

    public function chose()
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

        $this->telegram->triggerCommand('start', $this->update);
    }
}
