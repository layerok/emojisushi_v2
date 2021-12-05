<?php namespace Layerok\TgMall\Classes;

use Str;

class CallbackQuery
{
    public $fns;
    public $config;
    public $chatId;
    public $callback;


    public function __construct($responseData)
    {
        $this->fns = new Functions();
        $this->chatId = $responseData->callback_query->message->chat->id;
        $this->callback = json_decode($responseData->callback_query->data);
    }

    public function runCallbackAction($name)
    {
        \Log::info('running callback: ' . $name);
        $name = str_replace('"', '', $name);
        $name = str_replace('_', ' ', $name);
        $class = "Layerok\\TgMall\\Classes\\Callback\\";
        $class .= Str::studly($name);
        \Log::info('Trying to find the class: ' . $class);
        if (class_exists($class)) {
            $instance = new $class();
            $instance->chatId = $this->chatId;
            $instance->callback = $this->callback;
            $instance->fns = $this->fns;

            $instance->run();
        } else {
            \Log::info('[ ' . $class . '] class is not found ');
        }
    }

    public function handle()
    {
        \Log::info('Обрабатывает callback query');
        \Log::info('Callback data: ' . json_encode($this->callback, JSON_UNESCAPED_UNICODE));

        $action = "Idle";
        if (is_string($this->callback)) {
            $action = $this->callback;
        }

        if (@!is_null($this->callback->tag)) {
            $action = $this->callback->tag;
        }



        $this->runCallbackAction($action);
    }
}
