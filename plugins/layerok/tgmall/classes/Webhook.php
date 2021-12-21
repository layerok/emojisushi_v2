<?php namespace Layerok\TgMall\Classes;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Callbacks\CheckoutHandler;
use Layerok\TgMall\Classes\Messages\CheckoutMessageHandler;
use Layerok\TgMall\Models\State;
use League\Event\Emitter;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Events\UpdateWasReceived;
use Log;

class Webhook
{
    public function __construct()
    {
        if (env('TERMINATE_TELEGRAM_COMMANDS')) {
            return;
        };
        $emitter = new Emitter();

        $emitter->addListener(UpdateWasReceived::class, function ($event) {
            $update = $event->getUpdate();
            $chat = $update->getChat();
            $telegram = $event->getTelegram();


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
                $stateData = $first->state;

                if ($stateData['callback_handler'] === CheckoutHandler::class) {
                    $handler = new CheckoutMessageHandler($telegram, $update);
                    $handler->handle($first);
                }
            }
        });

        Telegram::setEventEmitter($emitter);

        Telegram::commandsHandler(true);
    }
}
