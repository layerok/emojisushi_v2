<?php
namespace Layerok\TgMall\Classes\Traits;

trait Warn {
    public function warn($msg)
    {
        $msg = "[Callback handler error] " . $msg;
        \Log::warning($msg);

    }
}
