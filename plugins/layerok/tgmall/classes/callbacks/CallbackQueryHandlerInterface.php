<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

interface CallbackQueryHandlerInterface
{
    public function make(Api $telegram, Update $update, $arguments): void;

    public function getArguments(): array;

    public function validate(): bool;
}
