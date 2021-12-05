<?php namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Facades\Telegram;
use Layerok\TgMall\Models\Action as ActionModel;

class Webhook
{
    public function __construct()
    {
        /**
         * Input file where webhook requests, sent from the bot, come.
         **/

        $testMode = false;
        $sys = new Sys();
        $fns = new Functions();

        $responseBody = file_get_contents('php://input');
        $responseData = json_decode($responseBody);
        date_default_timezone_set("Europe/Kiev");


        \Log::info('--------------------');
        \Log::info('Пришел хук от телеги');
        \Log::info(json_encode($responseData, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));


        if ($testMode) {
            return;
        }

        $isCallbackQuery = !empty($responseData->callback_query->data);

        if ($isCallbackQuery) {
            $callbackQueryId = $responseData->callback_query->id;
            $callbackQueryText = "";
            $cb = new CallbackQuery($responseData);

            $cb->handle();
            Telegram::answerCallbackQuery($callbackQueryId, $callbackQueryText);
        } else {
            $chatId = $responseData->message->chat->id;
            $message = $responseData->message->text;
            //$messageId = $responseData->message->message_id;


            if ($message == "/start") {
                $sys->clearActions($chatId);
            }

            $actions = ActionModel::select('action_id')
                ->where('chat_id', '=', $chatId)
                ->get();


            if ($actions->count()) {
                $action = new Action($chatId, $actions->first()->action_id, $message);
                if ($action->clearActions) {
                    $sys->clearActions($chatId);
                };
            } else {
                $msg = new Message();
                $msg->handle($responseData);
            }
        }
    }
}
