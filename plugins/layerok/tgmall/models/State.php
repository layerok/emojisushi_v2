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

    public function setCommand($command)
    {
        $newState = array_merge(
            $this->state,
            ['command' => $command]
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

    public function setStep($step)
    {
        $newState = array_merge(
            $this->state,
            ['step' => $step]
        );
        $this->state = $newState;
        $this->save();
    }
}
