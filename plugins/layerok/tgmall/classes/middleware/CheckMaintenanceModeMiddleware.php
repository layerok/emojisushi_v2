<?php

namespace Layerok\TgMall\Classes\Middleware;

use Layerok\TgMall\Models\Settings;

class CheckMaintenanceModeMiddleware extends AbstractMiddleware
{
    public function run(): bool
    {
        $chat = $this->update->getChat();
        if (Settings::get('is_maintenance_mode', env('TG_MALL_IS_MAINTENANCE_MODE', false))) {
            if (Settings::get('developer_chat_id', env('TG_MALL_DEVELOPER_CHAT_ID', false)) == $chat->id
                && Settings::get('pass_developer', env('TG_MALL_PASS_DEVELOPER', false))) {
                // если мы хотим дебажить как админы
                return true;
            } else {
                return false;
            }
        }
        return true;
    }

    public function onFailed(): void
    {
        $chat = $this->update->getChat();
        $this->telegram->sendMessage([
            'text' =>  'Приносим наши извинения. Над ботом временно ведутся технические работы.' .
                ' А пока Вы можете воспользоваться нашим сайтом https://emojisushi.com.ua',
            'chat_id' => $chat->id
        ]);
    }
}
