<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

class State extends Model
{
    protected $table = 'layerok_tgmall_states';
    protected $primaryKey = 'id';

    protected $jsonable = ['state'];

    public $fillable = [
        'chat_id',
        'state',
    ];

    public $timestamps = true;
}
