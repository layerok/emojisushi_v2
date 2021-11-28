<?php
namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Models\Action;

class Sys
{
    public $fns;

    public function __construct()
    {
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
        Action::create($insert);
        return true;
    }

    // Method for processing and removing action
    public function clearActions($chatId): void
    {
        Action::where('chat_id', '=', $chatId)
            ->delete();
    }

}
