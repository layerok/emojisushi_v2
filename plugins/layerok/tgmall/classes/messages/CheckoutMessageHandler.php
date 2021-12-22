<?php namespace Layerok\TgMall\Classes\Messages;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Models\State;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\PaymentMethod;
use OFFLINE\Mall\Models\ShippingMethod;
use Telegram\Bot\Api;
use Telegram\Bot\Keyboard\Keyboard;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class CheckoutMessageHandler
{
    use Lang;
    /** @var Update */
    protected $update;
    /** @var Api */
    protected $telegram;

    protected $step;

    /** @var State */
    protected $state;

    /** @var Customer */
    protected $customer;
    /** @var Cart */
    protected $cart;

    protected $chat;

    protected $products;

    protected $text;

    protected $errors;

    public function __construct(Api $telegram, Update $update)
    {
        $this->update = $update;
        $this->telegram = $telegram;

        $this->chat = $this->update->getChat();
        $this->text = $this->update->getMessage()->text;
    }

    public function validate(): bool
    {
        if (!isset($this->step)) {
            \Log::error('step is not set for checkout');
            $this->state
                ->first()
                ->setStep(Constants::STEP_PHONE);
        }

        if ($this->step == Constants::STEP_PHONE) {
            $data = [
                'phone' => $this->text
            ];

            $rules = [
                'phone' => 'required|phoneUa',
            ];

            $messages = [
                'phone.required' => "Введите номер телефона",
                'phone.phoneUa' => "Введите номер телефона в формате +38(0xx)xxxxxxx",
            ];

            $validation = Validator::make($data, $rules, $messages);

            if ($validation->fails()) {
                $this->errors = $validation->errors()->get('phone');
                return false;
            }


        }
        return true;
    }

    public function handle(State $state)
    {

        $this->state = $state;

        $stateData = $this->state->state;

        $this->step = $stateData['step'] ?? null;

        $isValid = $this->validate();

        if (!$isValid) {
            $this->handleErrors();
            return;
        }

        $this->customer = Customer::where('tg_chat_id', '=', $this->chat->id)->first();

        if ($this->step == Constants::STEP_PHONE) {
            $this->customer->tg_phone = $this->text;
            $this->customer->save();

            $k = new Keyboard();
            $k->inline();

            $methods = PaymentMethod::orderBy('sort_order', 'ASC')->get();

            $methods->map(function ($item) use ($k) {
                $k->row($k::inlineButton([
                    'text' => $item->name,
                    'callback_data' => json_encode([
                        'name' => 'chose_payment_method',
                        'arguments' => [
                            'id' => $item->id
                        ]
                    ])
                ]));
            });

            \Telegram::sendMessage([
                'text' => $this->lang('chose_payment_method'),
                'chat_id' => $this->chat->id,
                'reply_markup' => $k
            ]);

            $this->state->setStep(Constants::STEP_PAYMENT);
        }
        elseif ($this->step == Constants::STEP_PAYMENT_CASH) {
            $k = new Keyboard();
            $k->inline();

            $methods = ShippingMethod::orderBy('sort_order', 'ASC')->get();

            $methods->map(function ($item) use ($k) {
                $k->row($k::inlineButton([
                    'text' => $item->name,
                    'callback_data' => json_encode([
                        'name' => 'chose_delivery_method',
                        'arguments' => [
                            'id' => $item->id
                        ]
                    ])
                ]));
            });
            \Telegram::sendMessage([
                'text' => 'Выберите тип доставки',
                'chat_id' => $this->update->getChat()->id,
                'reply_markup' => $k
            ]);
            $this->state->setStep(Constants::STEP_DELIVERY);
        }
        elseif ($this->step == Constants::STEP_DELIVERY_COURIER) {
            // самовывоз
            \Telegram::sendMessage([
                'text' => 'Комментарий к заказу',
                'chat_id' => $this->update->getChat()->id,
            ]);
            $this->state->setStep(Constants::STEP_COMMENT);
        } elseif ($this->step == Constants::STEP_COMMENT) {
            $k = new Keyboard();
            $k->inline();
            $yes = $k::inlineButton([
                'text' => 'Да',
                'callback_data' => json_encode([
                    'name' => 'confirm_order'
                ])
            ]);
            $no = $k::inlineButton([
                'text' => 'Нет',
                'callback_data' => json_encode([
                    'name' => 'start'
                ])
            ]);
            $k->row($yes, $no);


            \Telegram::sendMessage([
                'chat_id' => $this->chat->id,
                'text' => 'Подтвердить заказ?',
                'reply_markup' => $k
            ]);
        }
    }

    public function handleErrors()
    {
        foreach ($this->errors as $error) {
            Telegram::sendMessage([
                'text' => $error . '. Попробуйте снова.',
                'chat_id' => $this->chat->id
            ]);
        }
    }

}
