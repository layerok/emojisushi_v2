<?php namespace Layerok\TgMall\Classes\Middleware;

use Layerok\TgMall\Classes\Buttons\ChoseBranchButton;
use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Lovata\BaseCode\Models\Branches;
use OFFLINE\Mall\Models\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class CheckBranchMiddleware
{
    /**
     * @var Api
     */
    protected $telegram;
    /**
     * @var Update
     */
    protected $update;

    /** @var Customer */
    protected $customer;
    /**
     * @param Customer|null $customer
     */
    public function __construct(Api $telegram, Update $update)
    {
        $this->update = $update;
        $this->telegram = $telegram;
        $chat = $update->getChat();

        $this->customer = Customer::where('tg_chat_id', '=', $chat->id)->first();
    }

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

    public function onFailed()
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
