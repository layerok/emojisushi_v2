<?php namespace Layerok\TgMall\Commands;

class CheckoutCommand extends LayerokCommand
{
    protected $name = "checkout";
    protected $description = "Use this command to place the order";

    public function handle()
    {
        parent::before();
    }
}
