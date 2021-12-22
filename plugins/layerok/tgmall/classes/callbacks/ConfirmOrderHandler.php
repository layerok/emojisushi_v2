<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Illuminate\Support\Facades\Log;
use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\PaymentMethod;
use OFFLINE\Mall\Models\ShippingMethod;
use poster\src\PosterApi;
use Telegram\Bot\Keyboard\Keyboard;

class ConfirmOrderHandler extends CallbackQueryHandler
{
    use Lang;
    protected $products;
    protected $data;
    protected $middlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckCartMiddleware::class
    ];

    public function handle()
    {

        $this->prepareData();

        $this->sendTelegram($this->data);

        /*$result = $this->sendPoster($data);

            if (isset($result->error)) {
                $this->onPosterError($result);
                return false;
            }*/


        $this->cart->products()->delete();
        \Telegram::sendMessage([
            'text' => 'Спасибо. Ваш заказ принят в обработку. ' .
             'В ближайшее время наш менеджер свяжеться с Вами.',
            'chat_id' => $this->update->getChat()->id,
        ]);
        \Telegram::triggerCommand('start', $this->update);
    }

    public function prepareData()
    {
        $stateData = $this->state->state['order_info'];
        \Log::info($stateData);
        if (isset($stateData['payment_method_id'])) {
            $payment = PaymentMethod::find($stateData['payment_method_id'])->first();
            if (isset($payment)) {
                $this->data['payment'] = $payment->name;
            }
        }

        if (isset($stateData['delivery_method_id'])) {
            $delivery = ShippingMethod::find($stateData['delivery_method_id'])->first();
            if (isset($delivery)) {
                $this->data['delivery'] = $delivery->name;
            }
        }

        if (isset($stateData['comment'])) {
            $this->data['comment'] = $stateData['comment'];
        }

        if (isset($stateData['phone'])) {
            $this->data['phone'] = $stateData['phone'];
        } else {
            $this->data['phone'] = $this->customer->tg_phone;
        }

        if (isset($stateData['change'])) {
            $this->data['change'] = $stateData['change'];
        }

        $this->data['spot'] = $this->customer->branch->name;

        $this->cart = Cart::byUser($this->customer->user);
        $this->products = $this->cart->products()->get();


        if (!empty($this->customer->firstname)) {
            $this->data['first_name'] = $this->customer->firstname;
        }

        if (!empty($this->customer->lastname)) {
            $this->data['last_name'] = $this->customer->lastname;
        }


        $this->data['products'] = $this->posterProducts($this->products);
    }

    public function sendTelegram($data)
    {
        $telegramHelper = new \Lovata\BaseCode\Classes\Telegram();
        $message = $telegramHelper->getFormattedMessage('Новый заказ', $data);

        \Telegram::sendMessage([
            'text' => $message,
            'parse_mode' => "html",
            'chat_id' => env('TEST_CHAT_ID')//$customer->branch['telegram_chat_id']
        ]);
    }

    public function onPosterError($result)
    {
        Log::info("Ошибка при оформлении заказа: " . $result->message);
    }

    public function sendPoster($data)
    {
        PosterApi::init();
        return (object)PosterApi::incomingOrders()
            ->createIncomingOrder($data);
    }


    public function posterProducts($products): array
    {
        $poster_products = [];
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

            $poster_products[] = $productData;
        }
        return $poster_products;
    }
}
