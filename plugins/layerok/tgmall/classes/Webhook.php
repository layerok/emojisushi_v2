<?php namespace Layerok\TgMall\Classes;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Callbacks\CheckoutHandler;
use Layerok\TgMall\Classes\Callbacks\ChoseDeliveryMethodHandler;
use Layerok\TgMall\Classes\Callbacks\ChosePaymentMethodHandler;
use Layerok\TgMall\Classes\Messages\AbstractMessageHandler;
use Layerok\TgMall\Models\State;
use League\Event\Emitter;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Events\UpdateWasReceived;
use Log;
use Layerok\TgMall\Models\Settings;

class Webhook
{
    public function __construct()
    {
        if (Settings::get('turn_off', false)) {
            return;
        };
        $emitter = new Emitter();

        $emitter->addListener(UpdateWasReceived::class,
            function ($event) {
                $update = $event->getUpdate();
                $chat = $update->getChat();
                $telegram = $event->getTelegram();
                /*\Log::info($update);*/


                if ($update->isType('callback_query')) {
                    $data = json_decode($update->getCallbackQuery()->getData(), true);

                    $arguments = $data['arguments'] ?? [];
                    $name = $data['name'];

                    $bus = new CallbackQueryBus($telegram, $update);
                    $bus->process($name, $arguments);

                    Telegram::answerCallbackQuery([
                        'callback_query_id' => $update->getCallbackQuery()->id,
                    ]);
                }

                if ($update->isType('message')) {
                    if ($update->hasCommand()) {
                        return;
                    }

                    $state = State::where([
                        'chat_id' => $chat->id
                    ]);

                    if (!isset($state)) {
                        return;
                    }

                    $first = $state->first();

                    if (isset($first->state['message_handler'])) {
                        $message_handler = $first->state['message_handler'];
                        if (class_exists($message_handler)) {
                            $handler = new $message_handler();
                            $handler->make($telegram, $update, $first);
                            $handler->handle();
                        } else {
                            \Log::error('message handler with [' . $message_handler . '] does not exist');
                        }
                    }
                }
            });

        Telegram::setEventEmitter($emitter);

        Telegram::commandsHandler(true);
    }
}
