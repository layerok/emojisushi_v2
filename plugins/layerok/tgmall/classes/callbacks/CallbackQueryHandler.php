<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Telegram\Bot\Answers\Answerable;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

abstract class CallbackQueryHandler implements CallbackQueryHandlerInterface
{

    /**
     * @var Api
     */
    protected $telegram;

    /**
     * @var Update
     */
    protected $update;

    protected $arguments = [];

    abstract public function handle();

    public function getArguments(): array
    {
        return $this->arguments;
    }

    public function setArguments(array $arguments): self
    {
        $this->arguments = $arguments;

        return $this;
    }

    public function make(Api $telegram, Update $update)
    {
        $this->telegram = $telegram;
        $this->update = $update;

        return call_user_func_array([$this, 'handle'], array_values($this->getArguments()));
    }
}
