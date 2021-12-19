<?php namespace Layerok\TgMall\Commands;

class CheckoutCommand extends LayerokCommand
{
    protected $name = "checkout";
    protected $description = "Use this command to place the order";

    const PHONE = 1;
    const COMMENT = 2;

    public function handle()
    {
        parent::before();
        if (!isset($this->state->state['step'])) {
            $upd = ['step' => self::PHONE];

            $this->state->update([
                'state' => array_merge(
                    $this->state->state,
                    $upd
                )
            ]);
        }
        switch ($this->state->state['step']) {
            case self::PHONE:
                $this->replyWithMessage([
                    'text' => 'Введите Ваш телефон'
                ]);
                $upd = ['step' => self::COMMENT];

                $this->state->update([
                    'state' => array_merge(
                        $this->state->state,
                        $upd
                    )
                ]);
                break;
            case self::COMMENT:
                $this->replyWithMessage([
                    'text' => 'Введите Ваш комментарий к заказу'
                ]);
                break;
        }
    }


}
