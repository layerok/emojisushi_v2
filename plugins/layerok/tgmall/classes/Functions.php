<?php
namespace Layerok\TgMall\Classes;

use Illuminate\Support\Facades\Lang;
use Layerok\TgMall\Facades\Telegram;

class Functions
{
    public function getExtension($filename)
    {
        $explode = explode('.', $filename);
        return end($explode);
    }

    public function printMainMenu()
    {
        global $tg, $sqli, $chatId, $_text;
        $keyboard = new InlineKeyboard();
        $i = 1;
        foreach ($sqli->getArrayData("categories", "1") as $row) {
            $keyboard->addButton(
                $i,
                $row["title"],
                [
                    "tag" => "select_category", "category_id" => $row["id"],
                    "page" => 1
                ]
            );
            $i++;
        }
        $keyboard->addButton(
            $z,
            Lang::get("layerok.tgmall::telegram.in_menu_main"),
            "in_menu_main"
        );
        Telegram::sendMessage(
            $chatId,
            Lang::get("layerok.tgmall::telegram.menu_text"),
            $keyboard->printInlineKeyboard()
        );
    }


    public function askName($chatId)
    {
        \Telegram::sendMessage(
            $chatId,
            \Lang::get("layerok.tgmall::telegram.ask_name")
        );
        addAction(1, $chatId);

    }

    public function sendMainPanel1()
    {
        global $tg, $sqli, $chatId, $_text, $firstName;

        if(empty($firstName)) $name = \Lang::get("layerok.tgmall::telegram.added_start_text") . \Lang::get("layerok.tgmall::telegram.start_text");
        else  $name = $firstName . \Lang::get("layerok.tgmall::telegram.added_start_text_in_name") . \Lang::get("layerok.tgmall::telegram.start_text");

        $keyboard = new InlineKeyboard();
        $keyboard->addButton(1, \Lang::get("layerok.tgmall::telegram.menu"), "menu");
        $keyboard->addButton(1, \Lang::get("layerok.tgmall::telegram.busket"), "busket");
        $keyboard->addButton(2, \Lang::get("layerok.tgmall::telegram.delivery_and_pay"), "delivery_and_pay");
        $keyboard->addButton(2, \Lang::get("layerok.tgmall::telegram.my_order"), "my_order");
        $keyboard->addButton(3, \Lang::get("layerok.tgmall::telegram.review"), "review");
        $keyboard->addButton(3, \Lang::get("layerok.tgmall::telegram.contact"), "contact");
        $keyboard->addButton(4, \Lang::get("layerok.tgmall::telegram.public"), "https://docs.google.com/document/d/1NgRF1_AzGVr6QVYv6wyRUg8EbB1VRPYfpA1sEhSlHEM/edit?usp=sharing", "url");
        $tg->sendMessage($chatId,  $name, $keyboard->printInlineKeyboard());

        // $tg->sendMessage($chatId, "123");
    }

    public function sendNotifications($order)
    {
        global $sqli, $tg, $_text;

        $contactz = $sqli->selectData("contacts", "`chat_id` = " . $order["chat_id"]);


        $pointTitle = $sqli->selectData("points", "`id` = " . $contactz["point_id"], "title");

        $msg = "<b>" . \Lang::get("layerok.tgmall::telegram.new_order") . " №" . $order["id"] . "</b>\n\n";
        $msg .= \Lang::get("layerok.tgmall::telegram.dish") . "\n";

        $allAmount = 0;

        foreach($sqli->getArrayData("positions_in_order", "`order_id` = " . $order["id"]) as $row) {
            $position = $sqli->selectData("positions", "`id` = " . $row["position_id"]);
            $amount = $row["count"] * $position["amount"];
            $msg .= $position["title"] . " - " . $row["count"] . " " . \Lang::get("layerok.tgmall::telegram.measuring_system") . " (" . $position["amount"] . " " . \Lang::get("layerok.tgmall::telegram.valute") . " / " . $amount . " " . \Lang::get("layerok.tgmall::telegram.valute") . ")\n";
            $allAmount += $amount;
        }


        $percent = $order["discount"];
        $allAmountAfterDisc = $allAmount;
        $discountAmount = $allAmountAfterDisc * ($percent / 100);

        $allAmountAfterDisc -= $discountAmount;
        if($discountAmount != 0) {
            $amountText = " -$discountAmount грн. по промокоду `" . $order["promocode"] . "`";
        } else {
            $amountText = "0" . \Lang::get("layerok.tgmall::telegram.valute");
        }


        $msg .= "\n" . \Lang::get("layerok.tgmall::telegram.amount") . "\n" . \Lang::get("layerok.tgmall::telegram.fullprice") . $allAmount . \Lang::get("layerok.tgmall::telegram.valute") . "\n" . \Lang::get("layerok.tgmall::telegram.discount") . $amountText . "\n" . \Lang::get("layerok.tgmall::telegram.all") . $allAmountAfterDisc . \Lang::get("layerok.tgmall::telegram.valute");

        $msg .= "\n\n" . \Lang::get("layerok.tgmall::telegram.contact_text");
        $msg .= "\n" . \Lang::get("layerok.tgmall::telegram.name_text") . $contactz["name"];
        $msg .= "\n" . \Lang::get("layerok.tgmall::telegram.adress_text") . $contactz["address"];
        $msg .= "\n" . \Lang::get("layerok.tgmall::telegram.phone_text") . $contactz["telephone"];

        $msg .= "" . \Lang::get("layerok.tgmall::telegram.pay_type");

        if($order["pay_type"] == 1) $msg .= \Lang::get("layerok.tgmall::telegram.pay_online");
        else $msg .= \Lang::get("layerok.tgmall::telegram.pay_offline");


        $q = [
            "chat_id" => $order["chat_id"],
            "msg" => base64_encode($msg),
            "pay_type" => 0,
            "date" => date("d.m.Y H:i"),
        ];
        $id = $sqli->insertData("old_orders", $q);


        foreach($sqli->getArrayData("admins", "`point_id` = " . $contactz["point_id"]) as $row) {
            $tg->sendMessage($row["chat_id"], $msg);
        }

    }

    public function addPositionInBasket($chatId, $positionId, $countPosition)
    {
        global $sqli, $tg;


        $orderId = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1", "id");
        if(is_null($orderId)) {
            $orderId = $sqli->insertData("orders", ["chat_id" => $chatId]);
        }

        $positionCount = $sqli->selectData("positions_in_order", "`order_id` = $orderId AND `position_id` = $positionId", "count");

        if(is_null($positionCount)) {
            $insrt = [
                "order_id" => $orderId,
                "position_id" => $positionId,
                "count" => $countPosition
            ];
            $sqli->insertData("positions_in_order", $insrt);
        } else {
            $newCount = $positionCount + $countPosition;
            $upd = [
                "count" => $newCount
            ];
            $sqli->updateData("positions_in_order", $upd, "`order_id` = $orderId AND `position_id` = $positionId");
        }

    }

    public function loadBasket($chatId)
    {
        global $sqli, $tg, $_text;

        $tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.busket"));


        $sendErrorMessage = true;
        $order = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1");
        if(!is_null($order["id"])) {
            $amountAll = 0;
            foreach($sqli->getArrayData("positions_in_order", "`order_id` = " . $order["id"]) as $row) {
                $countPosition = $row["count"];
                $position = $sqli->selectData("positions", "`id` = " . $row["position_id"]);

                $positionAmount = $position["amount"] * $countPosition;

                $amountAll += $positionAmount;

                $nextPage = $countPosition + 1;
                $prevPage = $countPosition - 1;
                if($nextPage >= 10) $nextPage = 10;
                if($prevPage <= 1) $prevPage = 1;


                $sendErrorMessage = false;
                $k = new InlineKeyboard();
                $k->addButton(1, \Lang::get("layerok.tgmall::telegram.minus"), ["tag" => "position_count_basket" , "position_id" =>$position["id"], "count" => $prevPage]);
                $k->addButton(1, $countPosition . "/10", "count_form");
                $k->addButton(1, \Lang::get("layerok.tgmall::telegram.plus"), ["tag" => "position_count_basket" , "position_id" =>$position["id"], "count" => $nextPage]);
                $k->addButton(1, \Lang::get("layerok.tgmall::telegram.del"), ["tag" => "delete_position" , "position_in_order_id" =>$row["id"]]);

                $k->addButton(2, str_replace("*price*", $positionAmount, \Lang::get("layerok.tgmall::telegram.prise_position_basket")), "123");

                $tg->sendPhoto($chatId, $position["cover"], "<b>" . $position["title"] . "</b>\n\n" . $position["description"], $k->printInlineKeyboard());

            }
            if(!$sendErrorMessage) {

                $percent = $order["discount"];

                $discountAmount = round($amountAll * ($percent / 100), 1);

                $amountAll -= $discountAmount;

                if($discountAmount != 0) {
                    $amountText = $amountAll . " (-$discountAmount)";
                } else {
                    $amountText = $amountAll;
                }

                $k = new InlineKeyboard();
                $k->addButton(1,  str_replace("*price*", $amountText, \Lang::get("layerok.tgmall::telegram.all_amount_order")), "321");
                $k->addButton(2,  \Lang::get("layerok.tgmall::telegram.take_order"), "take_order");
                if(is_null($order["promocode"])) {
                    $k->addButton(3,  \Lang::get("layerok.tgmall::telegram.promocode"), "enter_promocode");
                } else {
                    $k->addButton(3,  \Lang::get("layerok.tgmall::telegram.promocode_is_active"), "promocode_is_active");
                }
                $k->addButton(4,  \Lang::get("layerok.tgmall::telegram.in_menu_main"), "in_menu_main");
                $dotMessage = json_decode($tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.rasd"), $k->printInlineKeyboard()))->result->message_id;

                $upd = [
                    "message_id" => $dotMessage
                ];
                $sqli->updateData("orders", $upd, "`chat_id` = $chatId AND `is_active` = 1");
            }
        }

        if($sendErrorMessage) {
            $tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.busket_is_empty"));
        }
    }
}
