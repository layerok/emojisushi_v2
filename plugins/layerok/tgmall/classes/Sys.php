<?php
namespace Layerok\TgMall\Classes;

class Sys
{
    public $fns;
    function __construct() {
        $this->fns = new Functions();
    }
    // Function for defining a new action
    public function addAction($index, $chatId):bool
    {
        $this->clearActions($chatId);
        $insert = [
            "chat_id" => $chatId,
            "action_id" => $index
        ];
        $sqli->insertData("actions", $insert);
        return true;
    }

    // Method for processing and removing action
    public function clearActions($chatId): void
    {
        \DB::table('layerok_tgmall_actions')->where('chat_id', '=', $chatId)->delete();
    }

    public function isCallbackQuery($data):bool
    {
        if (empty($data)) {
            return false;
        }
        return true;
    }
}
