<?php namespace Layerok\TgMall\Classes\Callback;

class ShowCategories implements Action
{
    public $fns;
    public function run()
    {
        $this->fns->printMainMenu();
    }
}
