<?php

namespace Layerok\TgMall\Classes\Callbacks;

use Illuminate\Support\Facades\Log;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Utils\CheckoutUtils;
use Layerok\TgMall\Classes\Utils\PriceUtils;
use Layerok\TgMall\Models\Settings;
use Lovata\BaseCode\Classes\Helper\PosterProducts;
use Lovata\BaseCode\Classes\Helper\Receipt;
use OFFLINE\Mall\Models\Cart;
use poster\src\PosterApi;
use Telegram\Bot\Keyboard\Keyboard;
use \Telegram\Bot\Api;

class ConfirmOrderHandler extends CallbackQueryHandler
{
    use Lang;
    protected $products;
    protected $data;
    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckNotChosenBranchMiddleware::class,
        \Layerok\TgMall\Classes\Middleware\CheckEmptyCartMiddleware::class
    ];

    public function handle()
    {


        $products = CheckoutUtils::getProducts($this->cart, $this->state);
        $phone = CheckoutUtils::getPhone($this->customer, $this->state);
        $firstName = CheckoutUtils::getFirstName($this->customer);
        $lastName = CheckoutUtils::getLastName($this->customer);
        $address = CheckoutUtils::getCLientAddress($this->state);

        $receipt = new Receipt();
        $receipt->headline("Новый заказ");
        $receipt->make([
            'first_name' => $firstName,
            'last_name' => $lastName,
            'phone' => $phone,
            'address' => $address,
            'comment' => CheckoutUtils::getComment($this->state),
            'payment_method_name' => CheckoutUtils::getPaymentMethodName($this->state),
            'delivery_method_name' => CheckoutUtils::getDeliveryMethodName($this->state),
            'change' => CheckoutUtils::getChange($this->state),
            'spot_name' => $this->customer->branch->name,
            'products' => $products,
            'total' => PriceUtils::formattedCartTotal($this->cart)
        ]);
        $this->sendTelegram($receipt->getText());

        $result = $this->sendPoster([
            'spot_id' => CheckoutUtils::getSpotId($this->customer->branch),
            'phone' => $phone,
            'products' => $products,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'comment' => CheckoutUtils::getPosterComment($this->state),
            'address' => $address
        ]);

        if (isset($result->error)) {
            $this->onPosterError($result);
            return false;
        }

        $this->onPosterSuccess();
    }


    public function sendTelegram($message)
    {
        if (Settings::get('test_mode', env('TG_MALL_TEST_MODE', false))) {
            $bot_token = \Config::get('layerok.tgmall::test_bot_token');
            $chat_id = Settings::get('test_chat_id', '');
        } else {
            $bot_token = env('TELEGRAM_BOT_ID');
            $chat_id = $this->customer->branch->getChatId();
        }

        if (empty($chat_id) || empty($bot_token)) {
            return;
        }

        $api = new Api($bot_token);

        try {
            $api->sendMessage([
                'text' => $message,
                'parse_mode' => "html",
                'chat_id' =>  $chat_id
            ]);
        } catch (\Exception $exception) {
            Log::error((string)$exception);
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

    protected function onPosterSuccess()
    {
        $k = new Keyboard();
        $k->inline();
        $k->row($k::inlineButton([
            'text' => self::lang('in_menu_main'),
            'callback_data' => json_encode([
                'name' => 'start'
            ])
        ]));

        $this->cart->products()->delete();
        $this->telegram->sendMessage([
            'text' => 'Спасибо. Ваш заказ принят в обработку. ' .
                'В ближайшее время наш менеджер свяжеться с Вами.',
            'chat_id' => $this->update->getChat()->id,
            'reply_markup' => $k
        ]);
    }

}
