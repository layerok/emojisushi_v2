<?php
namespace Layerok\TgMall\Classes\Traits;

trait Warn {
    public function warn($msg)
    {
        $msg = "[Command Error] " . $msg;
        \Log::warning($msg);
        $this->replyWithMessage([
            'parse_mode' => 'html',
            'text' => $msg
        ]);
    }
}
