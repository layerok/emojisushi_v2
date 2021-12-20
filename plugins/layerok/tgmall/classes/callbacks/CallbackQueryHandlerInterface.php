<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

interface CallbackQueryHandlerInterface
{
    public function make(Api $telegram, Update $update);

    public function getArguments(): array;
}
