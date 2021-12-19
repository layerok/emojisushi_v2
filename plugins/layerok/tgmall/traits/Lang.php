<?php namespace Layerok\TgMall\Traits;

trait Lang
{
    private function lang($key)
    {
        return \Illuminate\Support\Facades\Lang::get('layerok.tgmall::lang.telegram.' . $key);
    }

}
