<?php

	/*

	$isProccess = true; // A flag that determines whether the action will be processed after receiving a message

	*/


	if($actionId == 1) {
		if(!$sqli->inDatabase("contacts", "`chat_id` = $chatId")) {
			$sqli->insertData("contacts", ["chat_id" => $chatId]);
		}
		$upd = [
			"name" => $message
		];
		$sqli->updateData("contacts", $upd, "`chat_id` = $chatId");

		// $z = 1;
		// $k = new InlineKeyboard();
		// foreach($sqli->getArrayData("points", "1") as $row) {
			// $k->addButton($z, $row["title"], ["tag" => "select_point", "point_id" => $row["id"]]);
			// $z++;
		// }

		// $tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.ask_point"), $k->printInlineKeyboard());


		$tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.ask_address"));
		addAction(2);
		$isProccess = false;


	} else if($actionId == 2) {
		$upd = [
			"address" => $message
		];
		$sqli->updateData("contacts", $upd, "`chat_id` = $chatId");
		$tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.ask_telephone"));
		addAction(3);
		$isProccess = false;
	} else if($actionId == 3) {
		$upd = [
			"telephone" => $message
		];
		$sqli->updateData("contacts", $upd, "`chat_id` = $chatId");

		$k = new InlineKeyboard();

		$k->addButton(1, \Lang::get("layerok.tgmall::telegram.pay_online"), ["tag" => "pay_select", "type" => "online"]);
		$k->addButton(2, \Lang::get("layerok.tgmall::telegram.pay_offline"), ["tag" => "pay_select", "type" => "offline"]);

		$tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.ask_pay"), $k->printInlineKeyboard());
	} else if($actionId == 4) {

		$insrt = [
			"text" => $message,
			"is_active" => 0
		];
		$sqli->updateData("reviews", $insrt, "`chat_id` = $chatId AND `is_active` = 1");
		$k = new InlineKeyboard();
		$k->addButton(1, \Lang::get("layerok.tgmall::telegram.in_menu_main"), "in_menu_main");
		$tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.end_review"), $k->printInlineKeyboard());
	} else if($actionId == 5) {
		$sendError = true;
		if($sqli->inDatabase("discounts", "`code` = '$message'")) {
			if(!$sqli->inDatabase("orders", "`promocode` = '$message'")) {
				$k = new InlineKeyboard();
				$k->addButton(1, \Lang::get("layerok.tgmall::telegram.busket"), "load_basket");
				$tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.access_promocode"), $k->printInlineKeyboard());
				$sendError = false;
				$discountPercent = $sqli->selectData("discounts", "`code` = '$message'", "percent");
				$upd = [
					"promocode" => $message,
					"discount" => $discountPercent
				];
				$sqli->updateData("orders", $upd, "`chat_id` = $chatId AND `is_active` = 1");
			}
		}

		if($sendError) {
			$k = new InlineKeyboard();
			$k->addButton(1, \Lang::get("layerok.tgmall::telegram.cancel"), "cancel_promocode");
			$tg->sendMessage($chatId, \Lang::get("layerok.tgmall::telegram.no_promocode"), $k->printInlineKeyboard());
			$isProccess = false;
		}
	}





























