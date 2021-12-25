<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Illuminate\Support\Facades\Log;
use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Utils\CheckoutUtils;
use Layerok\TgMall\Classes\Utils\PosterUtils;
use Lovata\BaseCode\Classes\Helper\ReceiptUtils;
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
    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckCartMiddleware::class
    ];

    public function handle()
    {
        $cart = Cart::byUser($this->customer->user);

        $this->data = CheckoutUtils::prepareData($this->state, $this->customer, $cart);

        $this->sendTelegram($this->data);

        /*$result = $this->sendPoster($data);

            if (isset($result->error)) {
                $this->onPosterError($result);
                return false;
            }*/

        $k = new Keyboard();
        $k->inline();
        $k->row($k::inlineButton([
            'text' => $this->lang('in_menu_main'),
            'callback_data' => json_encode([
                'name' => 'start'
            ])
        ]));

        $cart->products()->delete();
        \Telegram::sendMessage([
            'text' => 'Спасибо. Ваш заказ принят в обработку. ' .
             'В ближайшее время наш менеджер свяжеться с Вами.',
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => $k
        ]);
    }



    public function sendTelegram($data)
    {
        $message = ReceiptUtils::makeReceipt('Новый заказ', $data);
        $api = new \Telegram\Bot\Api(env('TELEGRAM_BOT_ID'));

        if (env('TG_MALL_TEST_MODE')) {
            $test_chat_id = env('TG_MALL_TEST_CHAT_ID');

            $api->sendMessage([
                'text' => $message,
                'parse_mode' => "html",
                'chat_id' => $test_chat_id
            ]);
        } else {
            $api->sendMessage([
                'text' => $message,
                'parse_mode' => "html",
                'chat_id' => $this->customer->branch['telegram_chat_id']
            ]);
        }
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

}
