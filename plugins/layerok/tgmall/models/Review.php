<?php namespace Layerok\TgMall\Models;

use October\Rain\Database\Model;

class Review extends Model
{
    protected $table = 'layerok_tgmall_reviews';
    protected $primaryKey = 'id';

    public $fillable = [
        'chat_id', 'is_active', 'point_title', 'text'
    ];

    public $timestamps = true;

}
