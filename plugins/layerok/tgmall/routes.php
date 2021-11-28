<?php
use Layerok\TgMall\Facades\Telegram;
use Layerok\TgMall\Classes\Functions;
use Layerok\TgMall\Classes\Action;
use Layerok\TgMall\Classes\Message;
use Layerok\TgMall\Classes\Sys;
use Illuminate\Support\Facades\Route;
use Layerok\TgMall\Classes\CallbackQuery;
use Layerok\TgMall\Models\Contact;
use Layerok\TgMall\Models\Action as ActionModel;

$botToken = Config::get('layerok.tgmall::botToken');
$webhookUrl = '/webhook' . $botToken;

Route::post($webhookUrl, function () {
    /**
     * Input file where webhook requests, sent from the bot, come.
     **/

    $testMode = true;
    $sys = new Sys();
    $fns = new Functions();

    $responseBody = file_get_contents('php://input');
    $responseData = json_decode($responseBody);
    date_default_timezone_set("Europe/Kiev");



    if ($testMode) {
        $data = json_encode([
                date('m.d.Y h:i:s', time()) => $responseData
            ]) . "\n";
        \Log::info($data);
        return;
    }


    if ($fns->isCallbackQuery($responseData)) {
        $callbackQueryId = $responseData->callback_query->id;
        $callbackQueryText = "";
        $cb = new CallbackQuery();

        $cb->handle($responseData);
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
});



Route::get('/test-tgmall', function () {
    //\Log::info(Telegram::getAuthor());

    $action = \DB::table('layerok_tgmall_actions')
        ->select('action_id')
        ->first();

    $contact = Contact::where('chat_id', '=', 2)->first();


    dd($contact);
});
