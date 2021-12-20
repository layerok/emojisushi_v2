<?php namespace Layerok\TgMall\Classes\Callbacks;

use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class CallbackQueryBus
{
    /** @var Update  */
    protected $update;
    /** @var Api  */
    protected $telegram;

    public function __construct(Api $telegram, Update $update)
    {
        $this->telegram = $telegram;
        $this->update = $update;
    }

    public function process($name, $arguments = [])
    {
        $namespace = "Layerok\\TgMall\\Classes\\Callbacks\\";
        $class = \Str::studly($name);
        $namespacedClass = $namespace . $class . "Handler";

        if (class_exists($namespacedClass)) {
            $inst = new $namespacedClass();
            $inst->setArguments($arguments);
            $inst->make($this->telegram, $this->update);
        } else {
            \Log::error(["class that handles [$name] callback does not exist", $namespacedClass]);
        }
    }

    public function getUpdate(): Update
    {
        return $this->update;
    }
}
