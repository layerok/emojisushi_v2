<?php

namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Facades\Telegram;

class Message
{

    public $fns;

    public function __construct()
    {
        $this->fns = new Functions();
    }

    public function handle($responseData)
    {

        $chatId = $responseData->message->chat->id;
        $message = $responseData->message->text;
        $firstName = $responseData->message->from->first_name;
        $username = $responseData->message->from->username;

        if(max($responseData->message->photo)->file_id != "" && $sqli->inDatabase("admins", "`chat_id` = $chatId"))   {
            Telegram::sendMessage($chatId, max($responseData->message->photo)->file_id);
            exit();
        } else if($responseData->message->animation->file_id != "") {
            Telegram::sendMessage($chatId, $responseData->message->animation->file_id);
            exit();
        }

        if($message == "/test") {
            $photo = "https://webmaster-shulyak.ru/works/test-shop/admin/actions/temp-img/test.jpg";

            $sf = Telegram::sendPhoto($chatId, $photo);

            Telegram::sendMessage($chatId, $sf);
        }

        if($message == "/test2") {
            $photo = "https://m.mac-cosmetics.ru/media/export/cms/products/640x600/mac_sku_M2LPHW_640x600_0.jpg";

            $sf = Telegram::sendPhoto($chatId, $photo);

            Telegram::sendMessage($chatId, $sf);
        }

        if($message == "/start") {
            if(!$sqli->inDatabase("users", "chat_id = $chatId")) {
                $insert = [
                    "chat_id" => $chatId,
                    "first_name" => $firstName,
                    "username" => $username
                ];
                $sqli->insertData("users", $insert);
            }

            $this->fns->sendMainPanel1();

        } else if($message == \Lang::get('layerok.tgmall::telegram.review')) {

            $z = 1;
            $k = new InlineKeyboard();
            foreach($sqli->getArrayData("points", "1") as $row) {
                $k->addButton($z, $row["title"], ["tag" => "add_review", "point_id" => $row["id"]]);
                $z++;
            }

            Telegram::sendMessage($chatId, \Lang::get('layerok.tgmall::telegram.ask_review'), $k->printInlineKeyboard());

        } else if($message == \Lang::get('layerok.tgmall::telegram.menu')) {
            printMainMenu();
        } else if($message == \Lang::get('layerok.tgmall::telegram.contact')) {
            Telegram::sendMessage($chatId, \Lang::get('layerok.tgmall::telegram.zavernuli_contact'));
        } else if($message == \Lang::get('layerok.tgmall::telegram.delivery_and_pay')) {
            Telegram::sendMessage($chatId, \Lang::get('layerok.tgmall::telegram.delivery_and_pay_text'));
        } else if($message == \Lang::get('layerok.tgmall::telegram.my_order')) {


            $k = new InlineKeyboard();
            $z = 1;
            foreach($sqli->getArrayData("orders", "`chat_id` = $chatId AND `is_active` = 0") as $row) {
                $k->addButton($z, $row["date"], ["tag" => "load_old_order", "id" => $row["id"]]);
                $z++;
            }
            Telegram::sendMessage($chatId, \Lang::get('layerok.tgmall::telegram.old_order'), $k->printInlineKeyboard());

        } else if($message == \Lang::get('layerok.tgmall::telegram.busket')) {
            $this->fns->loadBasket($chatId);
        }
    }
}
