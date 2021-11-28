<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

class Action extends Model
{
    protected $table = 'layerok_tgmall_actions';
    protected $primaryKey = 'id';

    public $fillable = [
        'chat_id', 'action_id',
    ];

    public $timestamps = true;

}
