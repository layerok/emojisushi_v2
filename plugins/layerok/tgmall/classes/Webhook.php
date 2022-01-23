<?php namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use Layerok\TgMall\Classes\Commands\StartCommand;
use Layerok\TgMall\Classes\Commands\SupportCommand;
use Layerok\TgMall\Models\State;
use League\Event\Emitter;
use OFFLINE\Mall\Models\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Commands\HelpCommand;
use Telegram\Bot\Events\UpdateWasReceived;
use Log;
use Layerok\TgMall\Models\Settings;
use Telegram\Bot\Exceptions\TelegramResponseException;
use Telegram\Bot\Keyboard\Keyboard;

class Webhook
{
    public function __construct($bot_token)
    {

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


                    try {
                        $telegram->answerCallbackQuery([
                            'callback_query_id' => $update->getCallbackQuery()->id,
                        ]);
                    } catch (TelegramResponseException $e) {
                        \Log::error($e);
                    }
                }

                if ($update->isType('message')) {
                    if ($update->hasCommand()) {
                        return;
                    }

                    $customer = Customer::where('tg_chat_id', '=', $chat->id)->first();

                    if (!isset($customer)) {
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

        $api = new Api($bot_token);
        $api->addCommand(StartCommand::class);
 /*       $api->addCommand(SupportCommand::class);*/
        $api->addCommand(HelpCommand::class);

        $api->setEventEmitter($emitter);

        $api->commandsHandler(true);
    }
}
