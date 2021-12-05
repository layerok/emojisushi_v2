<?php namespace Layerok\TgMall\Classes\Callback;

class ShowMenu implements Action
{
    public $fns;
    public $chatId;
    public function run()
    {
        $this->fns->printMainMenu($this->chatId);
    }
}
