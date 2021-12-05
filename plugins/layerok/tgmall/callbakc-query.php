<?php

/*

$callbackQueryText = ""; // The text that will be shown to the user after answering the inline button

*/

if($callback->tag == "select_category") {
    $categoryId = $callback->category_id;
    $page = $callback->page;
    $nextPage = $page + 1;
    $activeOrder = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1", "id");
    if(!is_null($activeOrder)) {
        $countPositionInOrder = " (" . $sqli->getCount("positions_in_order", "`order_id` = $activeOrder") . ")";
    } else {
        $countPositionInOrder = "";
    }
    $offset = ($page - 1) * $config["productsInPage"];
    $countPosition = $config["productsInPage"];
    $dataInArray = $sqli->getArrayData("positions", "`category_id` = $categoryId LIMIT $countPosition OFFSET $offset");
    $len = count($dataInArray);
    $i = 0;
    $sendBasket = false;
    foreach($dataInArray as $row) {
        $isFileId = false;
        if(getExtension($row["cover"]) == "jpg") {
            $photo =  $config["directory"] . "admin/actions/temp-img/" . $row["cover"];
        } else {
            $photo = $row["cover"];
            $isFileId = true;
        }
        $k = new InlineKeyboard();
        $orderId = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1", "id");
        $printMainButtons = false;
        if(!is_null($orderId)) {
            $positionInBasket = $sqli->selectData("positions_in_order", "`order_id` = $orderId AND `position_id` = " . $row["id"]);
            if(!is_null($positionInBasket)) {
                $printMainButtons = true;
            } else {
                $printMainButtons = false;
            }
        } else {

            $printMainButtons = false;
        }
        if($printMainButtons) {
            $k->addButton(1, $_text["minus"], ["tag" => "position_count" , "position_id" =>$row["id"], "count" => 1]);
            $k->addButton(1,$positionInBasket["count"] . "/10", "count_form");
            $k->addButton(1, $_text["plus"], ["tag" => "position_count" , "position_id" =>$row["id"], "count" => 2]);
            $k->addButton(2, $_text["position_in_basket"], "position_in_basket");
        } else {
            $k->addButton(1, $_text["minus"], ["tag" => "position_count" , "position_id" =>$row["id"], "count" => 1]);
            $k->addButton(1,"1/10", "count_form");
            $k->addButton(1, $_text["plus"], ["tag" => "position_count" , "position_id" =>$row["id"], "count" => 2]);
            $k->addButton(2, str_replace("*price*", $row["amount"], $_text["in_basket_button_title"]), ["tag" => "add_in_basket", "count" => 1, "position_id" => $row["id"]]);
        }
        if ($i == $len - 1) {
            if($sqli->selectData("positions", "`category_id` = $categoryId ORDER BY id DESC LIMIT 1", "id") != $row["id"]) {
                $k->addButton(3, $_text["load_other_position"], ["tag" => "select_category", "category_id" => $categoryId, "page" => $nextPage]);
            }

            $sendBasket = true;
        }
        $photoData = json_decode($tg->sendPhoto($chatId, $photo, "<b>" . $row["title"] . "</b>\n\n" . $row["description"], $k->printInlineKeyboard()));

        if($sendBasket) {
            $key = new InlineKeyboard();
            $key->addButton(1, $_text["busket"] . $countPositionInOrder, "load_basket");
            $key->addButton(2, $_text["in_menu"], "in_menu");
            $key->addButton(3, $_text["in_menu_main"], "in_menu_main");
            $basketMsgId = json_decode($tg->sendMessage($chatId, $_text["triple_dot"], $key->printInlineKeyboard()))->result->message_id;;


            if(!$sqli->inDatabase("basket_message", "`chat_id` = $chatId")) {
                $msgid = [
                    "message_id" => $basketMsgId,
                    "chat_id" => $chatId
                ];
                $sqli->insertData("basket_message", $msgid);

            } else {
                $msgid = [
                    "message_id" => $basketMsgId,
                ];
                $sqli->updateData("basket_message", $msgid, "`chat_id` = $chatId");
            }
        }

        if(!$isFileId) {
            $sqli->updateData("positions", ["cover" => max($photoData->result->photo)->file_id], "`id` = " . $row["id"]);
            if (file_exists("admin/actions/temp-img/" . $row["cover"])) {
                unlink("admin/actions/temp-img/" . $row["cover"]);
            }
        }
        $i++;
    }
} else if($callback == "contact") {
    $k = new InlineKeyboard();
    $k->addButton(1, $_text["in_menu_main"], "in_menu_main");
    $tg->sendMessage($chatId, $sqli->selectData("config", "`id` = 2", "value_1"), $k->printInlineKeyboard());

} else if($callback == "review") {

    $z = 1;
    $k = new InlineKeyboard();
    foreach($sqli->getArrayData("points", "1") as $row) {
        $k->addButton($z, $row["title"], ["tag" => "add_review", "point_id" => $row["id"]]);
        $z++;
    }
    $k->addButton($z, $_text["in_menu_main"], "in_menu_main");
    $tg->sendMessage($chatId, $_text["ask_review"], $k->printInlineKeyboard());

} else if($callback == "my_order") {

    $k = new InlineKeyboard();
    $z = 1;
    foreach($sqli->getArrayData("orders", "`chat_id` = $chatId AND `is_active` = 0") as $row) {
        $k->addButton($z, $row["date"], ["tag" => "load_old_order", "id" => $row["id"]]);
        $z++;
    }
    if($z != 1) {
        $k->addButton($z, $_text["in_menu_main"], "in_menu_main");
        $tg->sendMessage($chatId, $_text["old_order"], $k->printInlineKeyboard());
    } else {
        $k->addButton(1, $_text["in_menu_main"], "in_menu_main");
        $tg->sendMessage($chatId, $_text["old_order_null"], $k->printInlineKeyboard());
    }

} else if($callback == "delivery_and_pay") {

    $k = new InlineKeyboard();
    $k->addButton(1, $_text["in_menu_main"], "in_menu_main");
    $tg->sendMessage($chatId, $sqli->selectData("config", "`id` = 1", "value_1"), $k->printInlineKeyboard());

} else if($callback == "busket") {

    loadBasket($chatId);

} else if($callback == "menu") {
    printMainMenu();

} else if($callback->tag == "delete_position") {
    $positionInBasketId = $callback->position_in_order_id;
    $sqli->deleteData("positions_in_order", "`id` = $positionInBasketId");
    $tg->deleteMessage($chatId, $messageId);

    $order = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1");
    $orderId = $order["id"];



    $amountAll = 0;

    foreach($sqli->getArrayData("positions_in_order", "`order_id` = $orderId") as $positionInBasket) {
        $count = $positionInBasket["count"];
        $amount = $sqli->selectData("positions", "`id` = " . $positionInBasket["position_id"], "amount");
        $amountAll += $count * $amount;
    }

    $k = new InlineKeyboard();
    $k->addButton(1,  str_replace("*price*", $amountAll, $_text["all_amount_order"]), "321");
    $k->addButton(2,  $_text["take_order"], "take_order");
    if(is_null($order["promocode"])) {
        $k->addButton(3,  $_text["promocode"], "enter_promocode");
    } else {
        $k->addButton(3,  $_text["promocode_is_active"], "promocode_is_active");
    }
    $k->addButton(4,  $_text["in_menu_main"], "in_menu_main");

    $tg->editMessageReplyMarkup($chatId, $order["message_id"], $k->printInlineKeyboard());

} else if($callback->tag == "position_count") {
    $positionCount = $callback->count;
    $positionId = $callback->position_id;
    if($positionCount <= 1) $positionCount = 1;
    if($positionCount >= 10) $positionCount = 10;
    $k = (array)$responseData->callback_query->message->reply_markup;
    // $k->addButton(2, str_replace("*price*", $row["amount"], $_text["in_basket_button_title"]), ["tag" => "add_in_basket", "count" => 1, "position_id" => $row["id"]]);
    // $k["inline_keyboard"][1][0] = ["text" => $k["inline_keyboard"][1][0]->text, "callback_data" => $jsonCallBack];
    if($k["inline_keyboard"][1][0]->text != $_text["position_in_basket"]) {
        $k["inline_keyboard"][0][0] = ["text" => $_text["minus"], "callback_data" => json_encode(["tag" => "position_count", "position_id" => $positionId, "count" => $positionCount-1])];
        $k["inline_keyboard"][0][1] = ["text" => $positionCount . "/10", "callback_data" => "count_form"];
        $k["inline_keyboard"][0][2] = ["text" => $_text["plus"], "callback_data" => json_encode(["tag" => "position_count", "position_id" => $positionId, "count" => $positionCount+1])];
        $callbackData = json_decode($k["inline_keyboard"][1][0]->callback_data);
        $callbackData->count = $positionCount;
        $jsonCallBack = json_encode($callbackData);
        $amount = $sqli->selectData("positions", "`id` = $positionId", "amount") * $positionCount;

        $k["inline_keyboard"][1][0] = ["text" => str_replace("*price*", $amount, $_text["in_basket_button_title"]), "callback_data" => $jsonCallBack];
    }
    $tg->editMessageReplyMarkup($chatId, $messageId, $k);
} else if($callback->tag == "add_review") {

    $pointId = $callback->point_id;
    $pointTitle = $sqli->selectData("points", "`id` = $pointId", "title");
    if(!$sqli->inDatabase("reviews", "`chat_id` = $chatId AND `is_active` = 1")) {
        $insrt = [
            "chat_id" => $chatId,
            "point_title" => $pointTitle,
            "date" => date("d.m.Y H:i")
        ];
        $sqli->insertData("reviews", $insrt);
    } else {
        $insrt = [
            "point_title" => $pointTitle,
            "date" => date("d.m.Y H:i")
        ];
        $sqli->updateData("reviews", $insrt, "`chat_id` = $chatId AND `is_active` = 1");
    }
    $tg->sendMessage($chatId, $_text["ask_review_text"]);
    addAction(4);

} else if($callback == "position_in_basket") {
    $callbackQueryText = $_text["warning_text_in_basket"];
} else if($callback->tag == "add_in_basket") {
    $positionId = $callback->position_id;
    $countPosition = $callback->count;
    addPositionInBasket($chatId, $positionId, $countPosition);
    $k = (array)$responseData->callback_query->message->reply_markup;
    $k["inline_keyboard"][1][0] = ["text" => $_text["position_in_basket"], "callback_data" => "position_in_basket"];
    // $z = 0;
    // foreach($k["inline_keyboard"] as $i) {
    // $messageFormat = explode(" ", $i[0]->text);
    // $keyboardText = $messageFormat[0] . " " . $messageFormat[1];
    // if($keyboardText == $_text["busket"]) {
    // $activeOrder = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1", "id");
    // if(!is_null($activeOrder)) {
    // $countPositionInOrder = " (" . $sqli->getCount("positions_in_order", "`order_id` = $activeOrder") . ")";
    // } else {
    // $countPositionInOrder = "";
    // }
    // $k["inline_keyboard"][$z][0] = ["text" => $_text["busket"] . $countPositionInOrder, "callback_data" => json_encode("load_basket")];

    // }
    // $z++;
    // }
    /// Добавить сюда +1 в корзину
    $basketMessageId = $sqli->selectData("basket_message", "`chat_id` = $chatId", "message_id");


    $activeOrder = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1", "id");
    if(!is_null($activeOrder)) {
        $countPos = $sqli->getCount("positions_in_order", "`order_id` = $activeOrder");
        if($countPos != 0) {
            $countPositionInOrder = " (" . $countPos . ")";
        } else {
            $countPositionInOrder = "";
        }
    } else {
        $countPositionInOrder = "";
    }

    $key = new InlineKeyboard();
    $key->addButton(1, $_text["busket"] . $countPositionInOrder, "load_basket");
    $key->addButton(2, $_text["in_menu"], "in_menu");
    $key->addButton(3, $_text["in_menu_main"], "in_menu_main");
    $tg->editMessageReplyMarkup($chatId, $basketMessageId, $key->printInlineKeyboard());
    // $tg->sendMessage($chatId, $basketMessageId);

    $tg->editMessageReplyMarkup($chatId, $messageId, $k);
} else if($callback == "promocode_is_active") {
    $callbackQueryText = $_text["promocode_is_active_text"];
} else if($callback == "in_menu_main") {

    sendMainPanel1();
} else if($callback == "in_menu") {
    printMainMenu();
} else if($callback == "load_basket") {
    loadBasket($chatId);
} else if($callback->tag == "position_count_basket") {
    $positionCount = $callback->count;
    $positionId = $callback->position_id;
    $order = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1");

    $position = $sqli->selectData("positions", "`id` = $positionId");

    $orderId = $order["id"];

    $sqli->updateData("positions_in_order", ["count" => $positionCount], "`order_id` = $orderId AND `position_id` = " . $positionId);

    $positionAmount = $position["amount"] * $positionCount;

    $nextPage = $positionCount + 1;
    $prevPage = $positionCount - 1;
    if($nextPage >= 10) $nextPage = 10;
    if($prevPage <= 1) $prevPage = 1;

    $k = (array)$responseData->callback_query->message->reply_markup;
    $k["inline_keyboard"][0][0] = ["text" => $_text["minus"], "callback_data" => json_encode(["tag" => "position_count_basket" , "position_id" =>$positionId, "count" => $prevPage])];
    $k["inline_keyboard"][0][1] = ["text" =>$positionCount . "/10", "callback_data" => "position_in_basket"];
    $k["inline_keyboard"][0][2] = ["text" => $_text["plus"], "callback_data" => json_encode(["tag" => "position_count_basket" , "position_id" =>$positionId, "count" => $nextPage])];

    $k["inline_keyboard"][1][0] = ["text" => str_replace("*price*", $positionAmount, $_text["prise_position_basket"]), "callback_data" => "123"];

    $tg->editMessageReplyMarkup($chatId, $messageId, $k);

    $amountAll = 0;

    foreach($sqli->getArrayData("positions_in_order", "`order_id` = $orderId") as $positionInBasket) {
        $count = $positionInBasket["count"];
        $amount = $sqli->selectData("positions", "`id` = " . $positionInBasket["position_id"], "amount");
        $amountAll += $count * $amount;
    }

    $percent = $order["discount"];

    $discountAmount = $amountAll * ($percent / 100);

    $amountAll -= $discountAmount;
    if($discountAmount != 0) {
        $amountText = $amountAll . " (-$discountAmount)";
    } else {
        $amountText = $amountAll;
    }
    $k = new InlineKeyboard();
    $k->addButton(1,  str_replace("*price*", $amountText, $_text["all_amount_order"]), "321");
    $k->addButton(2,  $_text["take_order"], "take_order");
    if(is_null($order["promocode"])) {
        $k->addButton(3,  $_text["promocode"], "enter_promocode");
    } else {
        $k->addButton(3,  $_text["promocode_is_active"], "promocode_is_active");
    }
    $k->addButton(4,  $_text["in_menu_main"], "in_menu_main");

    $tg->editMessageReplyMarkup($chatId, $order["message_id"], $k->printInlineKeyboard());

} else if($callback == "enter_promocode") {

    $tg->sendMessage($chatId, $_text["ask_promocode"]);
    addAction(5);


} else if($callback == "cancel_promocode") {
    processAction();
    loadBasket($chatId);
} else if($callback == "take_order") {

    $activeOrder = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1", "id");
    if(!is_null($activeOrder)) {
        $countPos = $sqli->getCount("positions_in_order", "`order_id` = $activeOrder");
        if($countPos != 0) {

            // Оформление заказа

            askName();




        }
    }
} else if($callback->tag == "select_point") {
    if($sqli->inDatabase("orders", "`chat_id` = $chatId AND `is_active` = 1")) {
        $upd = [
            "point_id" => $callback->point_id
        ];
        $sqli->updateData("contacts", $upd, "`chat_id` = $chatId");


        addAction(2);
        $tg->sendMessage($chatId, $_text["ask_address"]);
    }

} else if($callback->tag == "load_old_order") {

    $orderId = $callback->id;

    $order = $sqli->selectData("orders", "`id` = $orderId");

    $msg = "<b>" . $order["date"] . "</b>\n\n" . $_text["dish"] . "\n";
    foreach($sqli->getArrayData("positions_in_order", "`order_id` = $orderId") as $row) {
        $positionName = $sqli->selectData("positions", "`id` = " . $row["position_id"], "title");
        $count = $row["count"];
        $msg .= "$positionName - $count " . $_text["measuring_system"] . "\n";
    }
    $msg .= $_text["pay_type"];
    if($order["pay_type"] == 0) $msg .= $_text["pay_offline"];
    else $msg .= $_text["pay_online"];
    $tg->sendMessage($chatId, $msg);
} else if($callback->tag == "pay_select") {
    if($sqli->inDatabase("orders", "`chat_id` = $chatId AND `is_active` = 1")) {
        if($callback->type == "online") {
            // $tg->sendMessage($chatId, $config["directory"] . "pay.php?chat_id=$chatId");
            // file_get_contents($config["directory"] . "pay.php?chat_id=$chatId");

        } else {
            date_default_timezone_set("Europe/Kiev");

            $order = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1");
            sendNotifications($order);

            $upd = [
                "is_active" => 0,
                "date" => date("d.m.Y H:i"),
                "pay_type" => 0
            ];

            $sqli->updateData("orders", $upd, "`chat_id` = $chatId AND `is_active` = 1");
            $k = new InlineKeyboard();
            $k->addButton(1, $_text["in_menu_main"], "in_menu_main");
            $tg->sendMessage($chatId, $_text["end_order"], $k->printInlineKeyboard());




        }
    }
}




























