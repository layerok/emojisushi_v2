<?php namespace Layerok\TgMall\Classes\Traits;

trait Lang
{
    private static function lang($key)
    {
        return \Illuminate\Support\Facades\Lang::get('layerok.tgmall::lang.telegram.' . $key);
    }


}
