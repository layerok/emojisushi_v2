<?php namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Classes\Callbacks\CallbackQueryBus;
use League\Event\Emitter;
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


            if ($update->detectType() === 'callback_query') {
                $data = json_decode($update->getCallbackQuery()->getData(), true);

                //\Log::info(['d' => $data]);
                $arguments = $data['arguments'] ?? [];
                $name = $data['name'];

                //\Log::info([$name, $arguments]);

                $bus = new CallbackQueryBus($telegram, $update);
                $bus->process($name, $arguments);

                Telegram::answerCallbackQuery([
                    'callback_query_id' => $update->getCallbackQuery()->id,
                ]);
            }

        });

        Telegram::setEventEmitter($emitter);

        Telegram::commandsHandler(true);

    }
}
