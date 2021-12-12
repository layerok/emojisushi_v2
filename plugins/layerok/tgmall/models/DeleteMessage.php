<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

class DeleteMessage extends Model
{
    protected $table = 'layerok_tgmall_delete_message';
    protected $primaryKey = 'id';

    public $fillable = [
        'chat_id',
        'msg_id',
    ];

    public $timestamps = true;

}
