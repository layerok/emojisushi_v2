<?php
namespace Layerok\TgMall\Classes;

class InlineKeyboard
{
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
