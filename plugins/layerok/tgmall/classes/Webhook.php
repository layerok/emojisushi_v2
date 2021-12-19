<?php namespace Layerok\TgMall\Classes;

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
            $telegram = $event->getTelegram();
            $chat = $update->getChat();
            $from = $update->getMessage()->getFrom();

            $customer = Customer::where('tg_chat_id', '=', $chat->id)->first();

            if (!$customer) {
                $pass = "qweasdqweasd";
                $user = User::create([
                    'name' => "jonh",
                    'password' => $pass,
                    'password_confirmation' => $pass
                ]);
                Customer::create([
                    "tg_chat_id" => $chat->id,
                    "firstname" => $from->firstName,
                    "lastname"  => $from->lastName,
                    "tg_username" => $from->username,
                    "user_id" => $user->id
                ]);
            }

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
                   $command, $hackedUpdate, []
                );
            }
        });

        Telegram::setEventEmitter($emitter);

        Telegram::commandsHandler(true);

        Log::debug('[---------END-----------');
    }
}
