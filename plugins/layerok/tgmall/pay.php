<?php

	if(isset($_REQUEST["chat_id"])) {

		$chatId = $_REQUEST["chat_id"];


		include("run-core.php");

		date_default_timezone_set("Europe/Kiev");

		$order = $sqli->selectData("orders", "`chat_id` = $chatId AND `is_active` = 1");
		sendNotificationsPay($order);

		$upd = [
			"is_active" => 0,
			"date" => date("d.m.Y H:i"),
			"pay_type" => 1
		];

		$sqli->updateData("orders", $upd, "`chat_id` = $chatId AND `is_active` = 1");


		$k = new InlineKeyboard();
		$k->addButton(1, \Lang::get("layerok.tgmall::telegram.in_menu_main"), "in_menu_main");

		$tg->sendMessage($chatId,\Lang::get("layerok.tgmall::telegram.pay_success"), $k->printInlineKeyboard());
	}


	function sendNotificationsPay($order) {
		global $sqli, $tg;



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


		$msg .= "\n" . \Lang::get("layerok.tgmall::telegram.amount") . "\n" . \Lang::get("layerok.tgmall::telegram.fullprice") . $allAmount . \Lang::get("layerok.tgmall::telegram.valute") . "\n" . \Lang::get("layerok.tgmall::telegram.discount") . $amountText . "\n" .\Lang::get("layerok.tgmall::telegram.all") . $allAmountAfterDisc . \Lang::get("layerok.tgmall::telegram.valute");


		$msg .= "\n\n" . \Lang::get("layerok.tgmall::telegram.contact_text");
		$msg .= "\n" . \Lang::get("layerok.tgmall::telegram.name_text") . $contactz["name"];
		$msg .= "\n" . \Lang::get("layerok.tgmall::telegram.adress_text") . $contactz["address"];
		$msg .= "\n" . \Lang::get("layerok.tgmall::telegram.phone_text") . $contactz["telephone"];

		$msg .= "" . \Lang::get("layerok.tgmall::telegram.pay_type");

		if($order["pay_type"] == 1) $msg .= \Lang::get("layerok.tgmall::telegram.pay_online");
		else $msg .= \Lang::get("layerok.tgmall::telegram.pay_online");

		$q = [
			"chat_id" => $order["chat_id"],
			"msg" => base64_encode($msg),
			"pay_type" => 1,
			"date" => date("d.m.Y H:i"),
		];
		$id = $sqli->insertData("old_orders", $q);

		foreach($sqli->getArrayData("admins", "`point_id` = " . $contactz["point_id"]) as $row) {
			$tg->sendMessage($row["chat_id"], $msg);
		}

	}
