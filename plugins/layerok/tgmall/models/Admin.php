<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

class Admin extends Model
{
    protected $table = 'layerok_tgmall_admins';
    protected $primaryKey = 'id';

    public $fillable = [
        'chat_id', 'point_id',
    ];

    public $timestamps = true;

}
