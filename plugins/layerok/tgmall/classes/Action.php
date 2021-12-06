<?php
namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Facades\Telegram;
use Layerok\TgMall\Models\Contact;
use Illuminate\Support\Facades\Lang;
use Layerok\TgMall\Models\Review;
use OFFLINE\Mall\Models\Customer;

class Action
{

    public $chatId;
    public $actionId;
    public $message;
    public $fns;
    public $sys;
    public $clearActions;

    public function __construct($chatId, $actionId, $message)
    {
        $this->chatId = $chatId;
        $this->actionId = $actionId;
        $this->message = $message;
        $this->fns = new Functions();
        $this->sys = new Sys();
        $this->clearActions= true;
    }

    public function handle()
    {
        if ($this->actionId == 1) {
            $contact = Customer::where('tg_chat_id', '=', $this->chatId)->first();
            if (!$contact) {
                $contact = Customer::create([
                    'tg_chat_id' => $this->chatId
                ]);
            }

            $contact->update(["name" => $this->message]);

            Telegram::sendMessage(
                Lang::get("layerok.tgmall::telegram.ask_address")
            );
            $this->sys->addAction(2, $this->chatId);
            $this->clearActions = false;
        } elseif ($this->actionId == 2) {
            Customer::where('tg_chat_id', '=', $this->chatId)
                ->update([
                    "address" => $this->message
                ]);

            Telegram::sendMessage(
                Lang::get("layerok.tgmall::telegram.ask_telephone")
            );
            $this->sys->addAction(3, $this->chatId);
            $this->clearActions = false;
        } elseif ($this->actionId == 3) {
            Customer::where('tg_chat_id', '=', $this->chatId)
                ->update([
                    "telephone" => $this->message
                ]);

            $k = new InlineKeyboard();

            $k->addButton(
                1,
                Lang::get("layerok.tgmall::telegram.pay_online"),
                ["tag" => "pay_select", "type" => "online"]
            );
            $k->addButton(
                2,
                Lang::get("layerok.tgmall::telegram.pay_offline"),
                ["tag" => "pay_select", "type" => "offline"]
            );

            Telegram::sendMessage(
                Lang::get("layerok.tgmall::telegram.ask_pay"),
                $k->printInlineKeyboard()
            );
        } elseif ($this->actionId == 4) {
            $upd = [
                "text" => $this->message,
                "is_active" => 0
            ];
            Review::where([
                ['chat_id', '=', $this->chatId],
                ['is_active', '=', 1]
            ])->update($upd);

            $k = new InlineKeyboard();
            $k->addButton(
                1,
                Lang::get("layerok.tgmall::telegram.in_menu_main"),
                "in_menu_main"
            );
            Telegram::sendMessage(
                Lang::get("layerok.tgmall::telegram.end_review"),
                $k->printInlineKeyboard()
            );
        }
    }
}
