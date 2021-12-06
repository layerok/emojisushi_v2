<?php namespace Layerok\TgMall\Classes\Callback;

class GoToMainMenu implements Action
{
    public $fns;
    public $response;

    public function run()
    {
        $first_name = $this->response->callback_query->from->first_name;
        $this->fns->sendMainPanel1($first_name);
    }
}
