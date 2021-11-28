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

//    public function printMainMenu()
//    {
//        $keyboard = new InlineKeyboard();
//        $i = 1;
//        foreach ($sqli->getArrayData("categories", "1") as $row) {
//            $keyboard->addButton(
//                $i,
//                $row["title"],
//                [
//                    "tag" => "select_category", "category_id" => $row["id"],
//                    "page" => 1
//                ]
//            );
//            $i++;
//        }
//        $keyboard->addButton(
//            $z,
//            Lang::get('layerok.tgmall::lang.telegram.in_menu_main'),
//            "in_menu_main"
//        );
//        Telegram::sendMessage(
//            $chatId,
//            Lang::get('layerok.tgmall::lang.telegram.menu_text'),
//            $keyboard->printInlineKeyboard()
//        );
//    }
//
//
//    public function askName($chatId)
//    {
//        Telegram::sendMessage(
//            $chatId,
//            Lang::get('layerok.tgmall::lang.telegram.ask_name')
//        );
//        addAction(1, $chatId);
//
//    }

    public function sendMainPanel1($chatId, $firstName)
    {
        \Log::info('inside sendMainPanel1 fn');
        if (empty($firstName)) {
            $name = Lang::get('layerok.tgmall::lang.telegram.added_start_text') .
                Lang::get('layerok.tgmall::lang.telegram.start_text');
        }
        else {
            $name = $firstName . Lang::get('layerok.tgmall::lang.telegram.added_start_text_in_name') .
                Lang::get('layerok.tgmall::lang.telegram.start_text');
        }

        $keyboard = new InlineKeyboard();
        $keyboard->addButton(1, Lang::get('layerok.tgmall::lang.telegram.menu'), "menu");
        $keyboard->addButton(1, Lang::get('layerok.tgmall::lang.telegram.busket'), "busket");
        $keyboard->addButton(2, Lang::get('layerok.tgmall::lang.telegram.delivery_and_pay'), "delivery_and_pay");
        $keyboard->addButton(2, Lang::get('layerok.tgmall::lang.telegram.my_order'), "my_order");
        $keyboard->addButton(3, Lang::get('layerok.tgmall::lang.telegram.review'), "review");
        $keyboard->addButton(3, Lang::get('layerok.tgmall::lang.telegram.contact'), "contact");

        Telegram::sendMessage($chatId, $name, $keyboard->printInlineKeyboard());
    }

//    public function sendNotifications($order)
//    {
//        global $sqli, $tg, $_text;
//
//        $contactz = $sqli->selectData("contacts", "`chat_id` = " . $order["chat_id"]);
//
//
//        $pointTitle = $sqli->selectData("points", "`id` = " . $contactz["point_id"], "title");
//
//        $msg = "<b>" . \Lang::get('layerok.tgmall::lang.telegram.new_order') . " №" . $order["id"] . "</b>\n\n";
//        $msg .= \Lang::get('layerok.tgmall::lang.telegram.dish') . "\n";
//
//        $allAmount = 0;
//
//        foreach($sqli->getArrayData("positions_in_order", "`order_id` = " . $order["id"]) as $row) {
//            $position = $sqli->selectData("positions", "`id` = " . $row["position_id"]);
//            $amount = $row["count"] * $position["amount"];
//            $msg .= $position["title"] . " - " . $row["count"] . " " . \Lang::get('layerok.tgmall::lang.telegram.measuring_system') . " (" . $position["amount"] . " " . \Lang::get('layerok.tgmall::lang.telegram.valute') . " / " . $amount . " " . \Lang::get('layerok.tgmall::lang.telegram.valute') . ")\n";
//            $allAmount += $amount;
//        }
//
//
//        $percent = $order["discount"];
//        $allAmountAfterDisc = $allAmount;
//        $discountAmount = $allAmountAfterDisc * ($percent / 100);
//
//        $allAmountAfterDisc -= $discountAmount;
//        if($discountAmount != 0) {
//            $amountText = " -$discountAmount грн. по промокоду `" . $order["promocode"] . "`";
//        } else {
//            $amountText = "0" . \Lang::get('layerok.tgmall::lang.telegram.valute');
//        }
//
//
//        $msg .= "\n" . \Lang::get('layerok.tgmall::lang.telegram.amount') . "\n" . \Lang::get('layerok.tgmall::lang.telegram.fullprice') . $allAmount . \Lang::get('layerok.tgmall::lang.telegram.valute') . "\n" . \Lang::get('layerok.tgmall::lang.telegram.discount') . $amountText . "\n" . \Lang::get('layerok.tgmall::lang.telegram.all') . $allAmountAfterDisc . \Lang::get('layerok.tgmall::lang.telegram.valute');
//
//        $msg .= "\n\n" . \Lang::get('layerok.tgmall::lang.telegram.contact_text');
//        $msg .= "\n" . \Lang::get('layerok.tgmall::lang.telegram.name_text') . $contactz["name"];
//        $msg .= "\n" . \Lang::get('layerok.tgmall::lang.telegram.adress_text') . $contactz["address"];
//        $msg .= "\n" . \Lang::get('layerok.tgmall::lang.telegram.phone_text') . $contactz["telephone"];
//
//        $msg .= "" . \Lang::get('layerok.tgmall::lang.telegram.pay_type');
//
//        if($order["pay_type"] == 1) $msg .= \Lang::get('layerok.tgmall::lang.telegram.pay_online');
//        else $msg .= \Lang::get('layerok.tgmall::lang.telegram.pay_offline');
//
//
//        $q = [
//            "chat_id" => $order["chat_id"],
//            "msg" => base64_encode($msg),
//            "pay_type" => 0,
//            "date" => date("d.m.Y H:i"),
//        ];
//        $id = $sqli->insertData("old_orders", $q);
//
//
//        foreach($sqli->getArrayData("admins", "`point_id` = " . $contactz["point_id"]) as $row) {
//            $tg->sendMessage($row["chat_id"], $msg);
//        }
//
//    }
//
//    public function addPositionInBasket($chatId, $positionId, $countPosition)
//    {
//        global $sqli, $tg;
//
//
//        $orderId = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1", "id");
//        if(is_null($orderId)) {
//            $orderId = $sqli->insertData("orders", ["chat_id" => $chatId]);
//        }
//
//        $positionCount = $sqli->selectData("positions_in_order", "`order_id` = $orderId AND `position_id` = $positionId", "count");
//
//        if(is_null($positionCount)) {
//            $insrt = [
//                "order_id" => $orderId,
//                "position_id" => $positionId,
//                "count" => $countPosition
//            ];
//            $sqli->insertData("positions_in_order", $insrt);
//        } else {
//            $newCount = $positionCount + $countPosition;
//            $upd = [
//                "count" => $newCount
//            ];
//            $sqli->updateData("positions_in_order", $upd, "`order_id` = $orderId AND `position_id` = $positionId");
//        }
//
//    }
//
//    public function loadBasket($chatId)
//    {
//        global $sqli, $tg, $_text;
//
//        $tg->sendMessage($chatId, \Lang::get('layerok.tgmall::lang.telegram.busket'));
//
//
//        $sendErrorMessage = true;
//        $order = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1");
//        if(!is_null($order["id"])) {
//            $amountAll = 0;
//            foreach($sqli->getArrayData("positions_in_order", "`order_id` = " . $order["id"]) as $row) {
//                $countPosition = $row["count"];
//                $position = $sqli->selectData("positions", "`id` = " . $row["position_id"]);
//
//                $positionAmount = $position["amount"] * $countPosition;
//
//                $amountAll += $positionAmount;
//
//                $nextPage = $countPosition + 1;
//                $prevPage = $countPosition - 1;
//                if($nextPage >= 10) $nextPage = 10;
//                if($prevPage <= 1) $prevPage = 1;
//
//
//                $sendErrorMessage = false;
//                $k = new InlineKeyboard();
//                $k->addButton(1, \Lang::get('layerok.tgmall::lang.telegram.minus'), ["tag" => "position_count_basket" , "position_id" =>$position["id"], "count" => $prevPage]);
//                $k->addButton(1, $countPosition . "/10", "count_form");
//                $k->addButton(1, \Lang::get('layerok.tgmall::lang.telegram.plus'), ["tag" => "position_count_basket" , "position_id" =>$position["id"], "count" => $nextPage]);
//                $k->addButton(1, \Lang::get('layerok.tgmall::lang.telegram.del'), ["tag" => "delete_position" , "position_in_order_id" =>$row["id"]]);
//
//                $k->addButton(2, str_replace("*price*", $positionAmount, \Lang::get('layerok.tgmall::lang.telegram.prise_position_basket')), "123");
//
//                $tg->sendPhoto($chatId, $position["cover"], "<b>" . $position["title"] . "</b>\n\n" . $position["description"], $k->printInlineKeyboard());
//
//            }
//            if(!$sendErrorMessage) {
//
//                $percent = $order["discount"];
//
//                $discountAmount = round($amountAll * ($percent / 100), 1);
//
//                $amountAll -= $discountAmount;
//
//                if($discountAmount != 0) {
//                    $amountText = $amountAll . " (-$discountAmount)";
//                } else {
//                    $amountText = $amountAll;
//                }
//
//                $k = new InlineKeyboard();
//                $k->addButton(1,  str_replace("*price*", $amountText, \Lang::get('layerok.tgmall::lang.telegram.all_amount_order')), "321");
//                $k->addButton(2,  \Lang::get('layerok.tgmall::lang.telegram.take_order'), "take_order");
//                if(is_null($order["promocode"])) {
//                    $k->addButton(3,  \Lang::get('layerok.tgmall::lang.telegram.promocode'), "enter_promocode");
//                } else {
//                    $k->addButton(3,  \Lang::get('layerok.tgmall::lang.telegram.promocode_is_active'), "promocode_is_active");
//                }
//                $k->addButton(4,  \Lang::get('layerok.tgmall::lang.telegram.in_menu_main'), "in_menu_main");
//                $dotMessage = json_decode($tg->sendMessage($chatId, \Lang::get('layerok.tgmall::lang.telegram.rasd'), $k->printInlineKeyboard()))->result->message_id;
//
//                $upd = [
//                    "message_id" => $dotMessage
//                ];
//                $sqli->updateData("orders", $upd, "`chat_id` = $chatId AND `is_active` = 1");
//            }
//        }
//
//        if($sendErrorMessage) {
//            $tg->sendMessage($chatId, \Lang::get('layerok.tgmall::lang.telegram.busket_is_empty'));
//        }
//    }

    public function isCallbackQuery($responseData):bool
    {
        if (empty($responseData->callback_query->data)) {
            return false;
        }
        return true;
    }
}
