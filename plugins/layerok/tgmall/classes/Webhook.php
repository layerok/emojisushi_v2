<?php namespace Layerok\TgMall\Classes;

use League\Event\Emitter;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Events\UpdateWasReceived;
use Log;
use Layerok\TgMall\Commands\StartCommand;
use Layerok\TgMall\Commands\MenuCommand;

class Webhook
{
    public function __construct()
    {

        $emitter = new Emitter();

        $emitter->addListener(UpdateWasReceived::class, function ($event) {
            $update = $event->getUpdate();
            $telegram = $event->getTelegram();
            Log::info('---------START-----------');
            Log::info('Пришел хук от телеги c типом [' . $update->detectType() . ']');
            Log::info($update->toJson());

            if ($update->detectType() === 'callback_query') {
                $callbackQuery = $update->getCallbackQuery();

                Telegram::answerCallbackQuery([
                    'callback_query_id' => $callbackQuery->id
                ]);

                Telegram::triggerCommand($callbackQuery->data, $update);
            }
        });

        Telegram::setEventEmitter($emitter);

        Telegram::addCommand(StartCommand::class);
        Telegram::addCommand(MenuCommand::class);

        Telegram::useWebhook();

        Log::info('[---------END-----------');
    }
}
