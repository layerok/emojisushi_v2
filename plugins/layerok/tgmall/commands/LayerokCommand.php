<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Models\State;
use Lovata\BaseCode\Models\Branches;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;
use Telegram\Bot\Commands\Command;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;

abstract class LayerokCommand extends Command
{

    /**
     * @var Customer
     */
    public $customer;
    /**
     * @var State
     */
    public $state;
    public function isSpotChosen(): bool
    {
        $branch = $this->customer->branch;
        if (!isset($branch)) {
            return false;
        }
        return true;
    }

    public function before($checkBranch = true)
    {
        $update = $this->getUpdate();
        $chat = $update->getChat();
        $from = $update->getMessage()->getFrom();

        $this->customer = Customer::where('tg_chat_id', '=', $chat->id)->first();

        if (!$this->customer) {
            $pass = "qweasdqweasd";
            $user = User::create([
                'name' => "jonh",
                'password' => $pass,
                'password_confirmation' => $pass
            ]);
            $this->customer = Customer::create([
                "tg_chat_id" => $chat->id,
                "firstname" => $from->firstName,
                "lastname"  => $from->lastName,
                "tg_username" => $from->username,
                "user_id" => $user->id
            ]);
        }

        $this->state = State::updateOrCreate(
            [
                'chat_id' => $chat->id,
            ],
            [
                'state' => [
                    'command' => $this->getName()
                ]
            ]
        )->first();

        if ($checkBranch && !$this->isSpotChosen()) {
            $this->triggerCommand('listbranch');
            exit;
        }
    }

    /**
     * {@inheritdoc}
     */
    abstract public function handle();
}
