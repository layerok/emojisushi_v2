<?php namespace Layerok\TgMall\Classes\Callbacks;


class NoopHandler extends CallbackQueryHandler
{
    public function handle()
    {
        return;
    }
}
