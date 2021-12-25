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
            $this->state ?? [],
            ['message_handler' => $handler]
        );
        $this->state = $newState;
        $this->save();
    }

    public function setOrderInfo($info)
    {
        $newState = array_merge(
            $this->state ?? [],
            ['order_info' => $info]
        );
        $this->state = $newState;
        $this->save();
    }

    public function mergeOrderInfo($info)
    {
        $newState = array_merge(
            $this->state ?? [],
            ['order_info' => array_merge(
                $this->state['order_info'] ?? [],
                $info
            )]
        );
        $this->state = $newState;
        $this->save();
    }

    public function setCallbackHandler($handler)
    {
        $newState = array_merge(
            $this->state ?? [],
            ['callback_handler' => $handler]
        );
        $this->state = $newState;
        $this->save();
    }

    public function setDeleteMsgInCategory($info)
    {
        $newState = array_merge(
            $this->state ?? [],
            ['delete_msg_in_category' => $info]
        );
        $this->state = $newState;
        $this->save();
    }

    public function getDeleteMsgInCategory()
    {
        $state = $this->state;
        if (!isset($state['delete_msg_in_category'])) {
            return null;
        }
        return $state['delete_msg_in_category'];
    }

    public function setCartCountMsg($info)
    {
        $newState = array_merge(
            $this->state ?? [],
            ['cart_count_msg' => $info]
        );
        $this->state = $newState;
        $this->save();
    }

    public function getCartCountMsg()
    {
        $state = $this->state;
        if (!isset($state['cart_count_msg'])) {
            return null;
        }
        return $state['cart_count_msg'];
    }

    public function setCartTotalMsg($info)
    {
        $newState = array_merge(
            $this->state ?? [],
            ['cart_total_msg' => $info]
        );
        $this->state = $newState;
        $this->save();
    }

    public function getCartTotalMsg()
    {
        $state = $this->state;
        if (!isset($state['cart_total_msg'])) {
            return null;
        }
        return $state['cart_total_msg'];
    }

}
