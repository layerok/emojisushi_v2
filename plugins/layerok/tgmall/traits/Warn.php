<?php
namespace Layerok\TgMall\Traits;

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
