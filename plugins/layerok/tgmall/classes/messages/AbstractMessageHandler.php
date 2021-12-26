<?php namespace Layerok\TgMall\Classes\Messages;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Models\State;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\PaymentMethod;
use OFFLINE\Mall\Models\ShippingMethod;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

abstract class AbstractMessageHandler implements MessageHandlerInterface
{
    use Lang;

    /** @var Update */
    protected $update;

    /** @var Api */
    protected $telegram;

    /** @var State */
    protected $state;

    protected $chat;

    protected $text;

    /** @var Customer */
    protected $customer;

    /** @var Cart */
    protected $cart;

    public function make(Api $telegram, Update $update, State $state)
    {
        $this->update = $update;
        $this->telegram = $telegram;
        $this->state = $state;

        $this->chat = $this->update->getChat();
        $this->text = $this->update->getMessage()->text;
        $this->customer = Customer::where('tg_chat_id', '=', $this->chat->id)->first();
        $this->cart = Cart::byUser($this->customer->user);
    }

    abstract public function handle();

}
