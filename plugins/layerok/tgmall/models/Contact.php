<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

class Contact extends Model
{
    protected $table = 'layerok_tgmall_contacts';
    protected $primaryKey = 'id';

    public $fillable = [
        'chat_id', 'name', 'point_id', 'address', 'telephone'
    ];

    public $timestamps = true;

}
