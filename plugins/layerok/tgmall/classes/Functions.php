<?php
namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Classes\Callback\Constants;
use Layerok\TgMall\Facades\Telegram;
use OFFLINE\Mall\Models\Category;

class Functions
{
    use \Layerok\TgMall\Traits\Lang;
    public function getExtension($filename)
    {
        $explode = explode('.', $filename);
        return end($explode);
    }

    public function printMainMenu()
    {
        $keyboard = new InlineKeyboard();
        $categories = Category::all();
        $categories->map(function ($row, $idx) use ($keyboard) {
            $keyboard->addButton(
                (int)$idx + 1,
                $row->name,
                [
                    "tag" => Constants::SHOW_PRODUCTS_BY_CATEGORY,
                    "category_id" => $row->id,
                    "page" => 1
                ]
            );
        });

        $keyboard->addButton(
            $categories->count() + 1,
            $this->lang('in_menu_main'),
            Constants::GO_TO_MAIN_MENU
        );
        Telegram::sendMessage(
            $this->lang('menu_text'),
            $keyboard->printInlineKeyboard()
        );
    }


//    public function askName($chatId)
//    {
//        Telegram::sendMessage(
//            $chatId,
//            $this->lang('ask_name')
//        );
//        addAction(1, $chatId);
//
//    }

    public function sendMainPanel1($firstName)
    {
        \Log::info('Начинаем формировать главное меню');
        if (empty($firstName)) {
            $name = $this->lang('added_start_text') .
                $this->lang('start_text');
        }
        else {
            $name = $firstName . $this->lang('added_start_text_in_name') .
                $this->lang('start_text');
        }

        $keyboard = new InlineKeyboard();
        $keyboard->addButton(1, $this->lang('menu'), Constants::SHOW_MENU);
        $keyboard->addButton(1, $this->lang('busket'), "busket");
        $keyboard->addButton(2, $this->lang('delivery_and_pay'), "delivery_and_pay");
        $keyboard->addButton(2, $this->lang('my_order'), "my_order");
        $keyboard->addButton(3, $this->lang('review'), "review");
        $keyboard->addButton(3, $this->lang('contact'), "contact");

        Telegram::sendMessage($name, $keyboard->printInlineKeyboard());
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
//        $msg = "<b>" . \$this->lang('new_order') . " №" . $order["id"] . "</b>\n\n";
//        $msg .= \$this->lang('dish') . "\n";
//
//        $allAmount = 0;
//
//        foreach($sqli->getArrayData("positions_in_order", "`order_id` = " . $order["id"]) as $row) {
//            $position = $sqli->selectData("positions", "`id` = " . $row["position_id"]);
//            $amount = $row["count"] * $position["amount"];
//            $msg .= $position["title"] . " - " . $row["count"] . " " . \$this->lang('measuring_system') . " (" . $position["amount"] . " " . \$this->lang('valute') . " / " . $amount . " " . \$this->lang('valute') . ")\n";
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
//            $amountText = "0" . \$this->lang('valute');
//        }
//
//
//        $msg .= "\n" . \$this->lang('amount') . "\n" . \$this->lang('fullprice') . $allAmount . \$this->lang('valute') . "\n" . \$this->lang('discount') . $amountText . "\n" . \$this->lang('all') . $allAmountAfterDisc . \$this->lang('valute');
//
//        $msg .= "\n\n" . \$this->lang('contact_text');
//        $msg .= "\n" . \$this->lang('name_text') . $contactz["name"];
//        $msg .= "\n" . \$this->lang('adress_text') . $contactz["address"];
//        $msg .= "\n" . \$this->lang('phone_text') . $contactz["telephone"];
//
//        $msg .= "" . \$this->lang('pay_type');
//
//        if($order["pay_type"] == 1) $msg .= \$this->lang('pay_online');
//        else $msg .= \$this->lang('pay_offline');
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
//        $tg->sendMessage($chatId, \$this->lang('busket'));
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
//                $k->addButton(1, \$this->lang('minus'), ["tag" => "position_count_basket" , "position_id" =>$position["id"], "count" => $prevPage]);
//                $k->addButton(1, $countPosition . "/10", "count_form");
//                $k->addButton(1, \$this->lang('plus'), ["tag" => "position_count_basket" , "position_id" =>$position["id"], "count" => $nextPage]);
//                $k->addButton(1, \$this->lang('del'), ["tag" => "delete_position" , "position_in_order_id" =>$row["id"]]);
//
//                $k->addButton(2, str_replace("*price*", $positionAmount, \$this->lang('prise_position_basket')), "123");
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
//                $k->addButton(1,  str_replace("*price*", $amountText, \$this->lang('all_amount_order')), "321");
//                $k->addButton(2,  \$this->lang('take_order'), "take_order");
//                if(is_null($order["promocode"])) {
//                    $k->addButton(3,  \$this->lang('promocode'), "enter_promocode");
//                } else {
//                    $k->addButton(3,  \$this->lang('promocode_is_active'), "promocode_is_active");
//                }
//                $k->addButton(4,  \$this->lang('in_menu_main'), "in_menu_main");
//                $dotMessage = json_decode($tg->sendMessage($chatId, \$this->lang('rasd'), $k->printInlineKeyboard()))->result->message_id;
//
//                $upd = [
//                    "message_id" => $dotMessage
//                ];
//                $sqli->updateData("orders", $upd, "`chat_id` = $chatId AND `is_active` = 1");
//            }
//        }
//
//        if($sendErrorMessage) {
//            $tg->sendMessage($chatId, \$this->lang('busket_is_empty'));
//        }
//    }

}
