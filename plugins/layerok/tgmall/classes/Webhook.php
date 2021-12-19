<?php namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Models\State;
use League\Event\Emitter;
use Lovata\BaseCode\Models\Branches;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;
use Telegram\Bot\Keyboard\Keyboard;
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

            if ($update->detectType() === 'callback_query') {
                $rawResponse = $update->getRawResponse();

                $callbackQueryId = $rawResponse['callback_query']['id'];

                $rawResponse['message'] = $rawResponse['callback_query']['message'];
                $rawResponse['message']['text'] = $rawResponse['callback_query']['data'];
                unset($rawResponse['callback_query']);

                $hackedUpdate = new \Telegram\Bot\Objects\Update($rawResponse);


                Telegram::answerCallbackQuery([
                    'callback_query_id' => $callbackQueryId
                ]);

                $command = ltrim(explode(' ', $rawResponse['message']['text'])[0], '/');

                if ($command === Constants::NOPE) {
                    return;
                }

                Telegram::getCommandBus()->execute(
                    $command,
                    $hackedUpdate,
                    []
                );
            } else {
                $text = $update->getMessage()->getText();
                $isCheckoutCommand = preg_match('/^\/checkout/', $text);
                if ($isCheckoutCommand) {
                    return;
                }

                $state = State::where('chat_id', '=', $chat->id);

                if (!$state->exists()) {
                    return;
                }

                $first = $state->first();

                //\Log::info($first);

                if (!isset($first->state['command'])) {
                    return;
                }

                if ($first->state['command'] == 'checkout') {
                   \Telegram::triggerCommand('checkout', $update);
                }
            }


        });

        Telegram::setEventEmitter($emitter);

        Telegram::commandsHandler(true);

        Log::debug('[---------END-----------');
    }
}
