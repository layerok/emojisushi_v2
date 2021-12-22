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

    public function setMessageHandler($handler)
    {
        $newState = array_merge(
            $this->state,
            ['message_handler' => $handler]
        );
        $this->state = $newState;
        $this->save();
    }

    public function setOrderInfo($info)
    {
        $newState = array_merge(
            $this->state,
            ['order_info' => $info]
        );
        $this->state = $newState;
        $this->save();
    }

    public function mergeOrderInfo($info)
    {
        $newState = array_merge(
            $this->state,
            ['order_info' => array_merge(
                $this->state['order_info'],
                $info
            )]
        );
        $this->state = $newState;
        $this->save();
    }

    public function setCallbackHandler($handler)
    {
        $newState = array_merge(
            $this->state,
            ['callback_handler' => $handler]
        );
        $this->state = $newState;
        $this->save();
    }

}
