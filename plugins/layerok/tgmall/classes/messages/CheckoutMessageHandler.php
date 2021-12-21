<?php namespace Layerok\TgMall\Classes\Messages;

use Illuminate\Support\Facades\Validator;
use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Models\State;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\Customer;
use Telegram\Bot\Api;
use Telegram\Bot\Laravel\Facades\Telegram;
use Telegram\Bot\Objects\Update;

class CheckoutMessageHandler
{
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

    public function __construct(Api $telegram, Update $update)
    {
        $this->update = $update;
        $this->telegram = $telegram;

    }

    public function validate(): bool
    {
        $chat = $this->update->getChat();
        if ($this->step == Constants::STEP_PHONE) {
            $text = $this->update->getMessage()->text;
            $data = [
                'phone' => $text
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
                $errors = $validation->errors()->get('phone');
                foreach ($errors as $error) {
                    Telegram::sendMessage([
                        'text' => $error . '. Попробуйте снова.',
                        'chat_id' => $chat->id
                    ]);
                }
                return false;
            }
            $this->customer = Customer::where('tg_chat_id', '=', $chat->id)->first();
            if (!isset($this->customer)) {
                \Telegram::sendMessage([
                    'text' => 'Ваш заказ пустой. Пожалуйста добавьте товар в корзину.',
                    'chat_id' => $chat->id
                ]);
                return false;
            }
            $this->cart = Cart::byUser($this->customer->user);
            $products = $this->cart->products()->get();

            if (!count($products) > 0) {
                \Telegram::sendMessage([
                    'text' => 'Ваш заказ пустой. Пожалуйста добавьте товар в корзину.',
                    'chat_id' => $chat->id
                ]);
                return false;
            }
        }
        return true;
    }

    public function handle(State $state)
    {

        $this->state = $state;
        $update = $this->update;
        $chat = $update->getChat();
        $telegram = $this->telegram;

        $stateData = $this->state->state;
        $step = $stateData['step'] ?? null;
        $this->step = $step;

        if (!isset($step)) {
            \Log::error('step is not set for checkout');
            $this->state->first()->setStep(Constants::STEP_PHONE);
        }

        $isValid = $this->validate();
        if (!$isValid) {
            return;
        }

        if ($step == Constants::STEP_PHONE) {
            $text = $update->getMessage()->text;
            $data = [
                'phone' => $text
            ];

            $products = $this->cart->products()->get();

            foreach ($products as $p) {
                $productData = [];
                if (isset($p['variant_id'])) {
                    $product = $p->product()->first();
                    $variant = $p->getItemDataAttribute();
                    $productData['modificator_id'] = $variant['poster_id'];
                } else {
                    $product = $p->getItemDataAttribute();
                }
                $productData['name'] = $product['name'];
                $productData['product_id'] = $product['poster_id'];
                $productData['count'] = $p['quantity'];

                $poster_data['products'][] = $productData;
            }


            $data['products'] = $poster_data['products'];


            $telegram_data = $data;
            $telegramHelper = new \Lovata\BaseCode\Classes\Telegram();
            $message = $telegramHelper->getFormattedMessage('Новый заказ', $telegram_data);



            /*         \Telegram::sendMessage([
                         'text' => $message,
                         'parse_mode' => "html",
                         'chat_id' => '-668888331'//$customer->branch['telegram_chat_id']
                     ]);*/

            \Log::info($message);

            \Telegram::sendMessage([
                'text' => 'Спасибо, Ваш заказ принят в работу.' .
                    ' Наш менеджер скоро с Вами свяжется.',
                'chat_id' => $chat->id
            ]);


            if (isset($this->cart)) {
                $this->cart->products()->delete();
            }


            $telegram->triggerCommand('start', $update);
        }
    }
}
