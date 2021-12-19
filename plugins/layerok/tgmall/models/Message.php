<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

class Message extends Model
{
    protected $table = 'layerok_tgmall_messages';
    protected $primaryKey = 'id';
    protected $jsonable = ['meta_data'];

    public $fillable = [
        'chat_id',
        'msg_id',
        'type',
        'meta_data'
    ];

    public $timestamps = true;

}
