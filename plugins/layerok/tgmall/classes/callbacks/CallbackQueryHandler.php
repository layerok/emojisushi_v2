<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Models\State;
use October\Rain\Exception\ValidationException;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;

use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;
use Layerok\TgMall\Models\Settings;

abstract class CallbackQueryHandler implements CallbackQueryHandlerInterface
{
    use Answerable;
    /**
     * @var Customer
     */
    protected $customer;
    /**
     * @var State
     */
    protected $state;

    /**
     * @var Cart
     */
    protected $cart;
    /**
     * @var Api
     */
    protected $telegram;

    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckMaintenanceModeMiddleware::class,
    ];

    protected $extendMiddlewares = [];

    /**
     * @var Update
     */
    protected $update;

    protected $arguments = [];

    abstract public function handle();

    public function validate(): bool
    {
        return true;
    }

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function make(Api $telegram, Update $update, $arguments): void
    {
        $this->telegram = $telegram;
        $this->update = $update;
        $this->arguments = $arguments;
        $chat = $this->update->getChat();
        $this->extendMiddlewares();
        foreach ($this->middlewares as $middleware) {
            $m = new $middleware();
            $m->make($this->telegram, $this->update);
            $isPassed = $m->run();
            if (!$isPassed) {
                /*\Log::info([get_class($m), ' middleware failed']);*/
                $m->onFailed();
                return;
            }
        }

        $this->before($update);

        $isValid = $this->validate();

        if (!$isValid) {
            return;
        }

        call_user_func_array([$this, 'handle'], array_values($this->getArguments()));
    }

    protected function extendMiddlewares()
    {
        $this->middlewares = array_merge(
            $this->middlewares,
            $this->extendMiddlewares
        );
    }

    public function before(Update $update)
    {

        $chat = $update->getChat();
        $from = $update->getCallbackQuery()->getFrom();


        $this->customer = Customer::where('tg_chat_id', '=', $chat->id)->first();
        if (!$this->customer) {
            $firstName = empty($from->getFirstName()) ? 'Не указано': $from->firstName;
            $lastName = empty($from->getLastName()) ? "": $from->lastName;
            $pass = str_random(8);
            $userData = [
                'name' => $firstName,
                'surname' => $lastName,
                'username' => $from->username,
                'password' => $pass,
                'password_confirmation' => $pass
            ];
            try {
                $user = User::create($userData);
            } catch (ValidationException $exception) {
                \Log::error([
                    'status' => 'error',
                    'msg'    => (string)$exception,
                    'errors' => $exception->getErrors()
                ]);
                return;
            }

            $customerData = [
                "tg_chat_id" => $chat->id,
                "firstname" => $firstName,
                "lastname"  => $lastName,
                "tg_username" => $from->username,
                "user_id" => $user->id
            ];
            try {
                $this->customer = Customer::create($customerData);
            } catch (ValidationException $exception) {
                \Log::error([
                    'status' => 'error',
                    'msg'    => (string)$exception,
                    'errors' => $exception->getErrors()
                ]);
                return;
            }

        }

        $this->cart = Cart::byUser($this->customer->user);

        $this->state = State::where('chat_id', '=', $chat->id)->first();

        if (!isset($this->state)) {
            $this->state = State::create([
                'chat_id' => $chat->id,
            ])->first();
        }
        $this->state->setCallbackHandler(get_class($this));

        //\Log::info(State::all());
    }
}
