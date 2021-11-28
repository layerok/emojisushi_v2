<?php

namespace Layerok\TgMall\Classes;

// A class that describes the interaction of the bot with the Telegram API
class Telegram
{

    public $botToken; // Bot token


    // Function for getting and saving the bot token
    // Accepts the token itself
    function __construct($token) {
        $this->botToken = $token;
    }

    public function getAuthor(): string
    {
        return "Layerok";
    }

    // Function for sending a simple message with the keyboard
    // Accepts the chat ID to send to, text, keyboard (Optional)
    // Example: sendMessage (123456789, "Hello", ['inline_keyboard' => [[["text" => 'Hello', "callback_data" => "hello"]]]]);
    // Parameters: if $ marmurkup = [0], then the keyboard will be cleared
    function sendMessage($chatId, $text, $markup=[]) {
        if(!empty($markup)) {
            if($markup[0] == "clear") {
                $markup = json_encode(['remove_keyboard' => true]);
            } else {
                $markup = json_encode($markup);
            }
        }
        $parameters = [
            "chat_id" 	=> $chatId,
            "text"    	=> $text,
            "reply_markup" => $markup,
            "parse_mode" => "HTML",
            "disable_web_page_preview" => false
        ];
        return $this->sendApiMethod("sendMessage", $parameters);
    }

    // Method for deleting a message
    // Accepts chat ID and message ID
    // Example: deleteMessage ($ chatID, $ data-> callback_query-> message-> message_id);
    function deleteMessage($chatId, $messageId) {
        $parameters = [
            "chat_id" 		=> $chatId,
            "message_id"    => $messageId
        ];
        return $this->sendApiMethod("deleteMessage", $parameters);
    }

    // Function for editing the keyboard
    // Accepts Chat ID, Message ID, Keyboard
    // Example: editMessageReplyMarkup (123456789, 645, []);
    function editMessageReplyMarkup($chatId, $messageId, $murkup=[]) {
        if(!empty($murkup)) {
            if($murkup[0] == "clear") {
                $murkup = json_encode(['remove_keyboard' => true]);
            } else {
                $murkup = json_encode($murkup);
            }
        }
        $parameters = [
            "chat_id" 	=> $chatId,
            "message_id" 	=> $messageId,
            "reply_markup" => $murkup,
        ];
        return $this->sendApiMethod("editMessageReplyMarkup", $parameters);
    }

    // Function for editing the message
    // Accepts Chat ID, Message ID, Text, Keyboard (Optional)
    // Example: editMessageText (123456789, 645, "New Text");
    function editMessageText($chatId, $messageId, $text, $murkup=[]) {
        if(!empty($murkup)) {
            if($murkup[0] == "clear") {
                $murkup = json_encode(['remove_keyboard' => true]);
            } else {
                $murkup = json_encode($murkup);
            }
        }
        $parameters = [
            "chat_id" 	=> $chatId,
            "message_id" 	=> $messageId,
            "text"    	=> $text,
            "reply_markup" => $murkup,
            "parse_mode" => "HTML",
            "disable_web_page_preview" => false
        ];
        return $this->sendApiMethod("editMessageText", $parameters);
    }

    // Method to send GIF or H.264 / MPEG-4 AVC video without sound
    // Accepts chat ID and file_id
    // Example: sendAnimation ($ chatId, $ fileId)
    function sendAnimation($chatId, $fileId, $caption="", $murkup=[]) {
        $parameters = array(
            "chat_id" => $chatId,
            "parse_mode" => "HTML",
            "caption" => $caption,
            "animation" => $fileId
        );

        if(!empty($murkup)) {
            if($murkup[0] == "clear") {
                $parameters += ['reply_markup' => json_encode(['remove_keyboard' => true])];
            } else {
                $parameters += ['reply_markup' => json_encode($murkup)];
            }
        }
        return $this->sendApiMethod("sendAnimation", $parameters);
    }


    // Method for sending pictures
    // Accepts chat ID and file_id
    // Example: sendPhoto ($ chatId, $ fileId)
    function sendPhoto($chatId, $fileId, $caption="", $murkup=[]) {
        $parameters = array(
            "chat_id" => $chatId,
            "parse_mode" => "HTML",
            "caption" => $caption,
            "photo" => $fileId
        );

        if(!empty($murkup)) {
            if($murkup[0] == "clear") {
                $parameters += ['reply_markup' => json_encode(['remove_keyboard' => true])];
            } else {
                $parameters += ['reply_markup' => json_encode($murkup)];
            }
        }
        return $this->sendApiMethod("sendPhoto", $parameters);
    }


    // Method for confirming inline button
    // Accepts ID inline button
    // Example: answerCallbackQuery ($ data-> callback_query-> id);
    function answerCallbackQuery($inlineButtonId, $text="") {
        $parameters = [
            "callback_query_id" 	=> $inlineButtonId
        ];
        if(!empty($text)) $parameters += ["text" => $text];
        return $this->sendApiMethod("answerCallbackQuery", $parameters);
    }


    // System method for sending API method via CURL
    // Accepts a method name as in the documentation and an array with parameters
    // Example: sendApiMethod ("sendMessage", $ parameters);
    function sendApiMethod($method, $parameters) {
        $ch = curl_init();
        $ln = "https://api.telegram.org/bot" . $this->botToken . "/" . $method . "?";
        curl_setopt($ch, CURLOPT_URL, $ln);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($parameters));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlResult = curl_exec($ch);
        curl_close($ch);

        return $curlResult;
    }

}

// A constructor class that assembles an inline keyboard and prepares it to be sent to the user
class InlineKeyboard {
    public $keyboard = []; // Main keyboard body

    // Method for adding a button to the keyboard
    // Accepts the line number where the button should be placed (from 1), button text, callback_data, button type (callback_data or URL)
    // Example: $keyboard->addButton(1, "Hello", "user_greeted")
    function addButton($row, $text, $callbackData, $type="callback_data") {
        if($row != 0) $row--;
        if($type != "url") $callbackData = json_encode($callbackData);
        $temp = ["text" => $text, $type => $callbackData];
        $this->keyboard[$row][] = $temp;

    }

    // Method for assembling keyboard and preparing for sending to user
    // Accepts nothing
    // Example: $keyboard->printInlineKeyboard();
    function printInlineKeyboard() {
        return ["inline_keyboard" => array_values($this->keyboard)];
    }
}

// Class for constructing a conventional embedded keyboard
class Keyboard {
    public $keyboard = []; // Main keyboard body

    // Method for adding a button to the keyboard
    // Accepts the line number where the button should be placed (from 1), button text
    // Example: $keyboard->addButton(1, "Hello")
    function addButton($row, $text) {
        if($row != 0) $row--;
        $this->keyboard[$row][] = $text;
    }

    // Method for assembling keyboard and preparing for sending to user
    // Accepts a flag whether to resize the keyboard (optional)
    // Example: $keyboard->printKeyboard(false);
    function printKeyboard($resizeKeyboard = true) {
        return [
            "keyboard" => array_values($this->keyboard),
            "resize_keyboard" => $resizeKeyboard
        ];
    }
}
?>
