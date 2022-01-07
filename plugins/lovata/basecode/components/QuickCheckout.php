<?php namespace Lovata\Basecode\Components;

use Illuminate\Support\Facades\Cache;
use Layerok\TgMall\Classes\Utils\PriceUtils;
use Lovata\BaseCode\Classes\Helper\PosterProducts;
use Lovata\BaseCode\Classes\Helper\PosterUtils;
use Lovata\BaseCode\Classes\Helper\Receipt;
use Symfony\Component\HttpFoundation\Session\Session;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use October\Rain\Exception\ValidationException;
use OFFLINE\Mall\Components\QuickCheckout as MallQuickCheckout;
use poster\src\PosterApi;
use Lovata\BaseCode\Models\Branches;
use Illuminate\Support\Facades\Log;
use \Telegram\Bot\Api;

/**
 * Class QuickCheckout
 * @package Lovata\Basecode\Components
 */
class QuickCheckout extends MallQuickCheckout
{
    public function componentDetails()
    {
        return [
            'name'        => 'QuickCheckout Component Extension',
            'description' => 'No description provided yet...'
        ];
    }



    public function defineProperties()
    {
        $parentProps = parent::defineProperties();
        $properties = array_merge(
            $parentProps,
            [
                'postPage' => [
                    'title'       => 'rainlab.blog::lang.settings.posts_post',
                    'description' => 'rainlab.blog::lang.settings.posts_post_description',
                    'type'        => 'dropdown',
                    'default'     => 'blog/post',
                    'group'       => 'Links',
                ],
            ]
        );
        return is_array($properties) ? $properties : $parentProps;
    }

    /**
     * Override of original method
     *
     * @return mixed
     */
    public function onSubmit()
    {
        $data = post();

        $rules = [
            'phone'             => 'required|phoneUa',
            'email'             => 'email|nullable',
        ];

        $messages = [
            'email.required'          => trans('offline.mall::lang.components.signup.errors.email.required'),
            'email.email'             => trans('offline.mall::lang.components.signup.errors.email.email'),
            'firstname.required'      => trans('offline.mall::lang.components.signup.errors.firstname.required'),

        ];

        $validation = Validator::make($data, $rules, $messages);

        if ($validation->fails()) {
            throw new ValidationException($validation);
        }

        $products = $this->cart->products()->get();

        if (!count($products) > 0) {
            throw new ValidationException(['Ваш заказ пустой. Пожалуйста добавьте товар в корзину.']);
        }

        $spots = Branches::all();

        if ($spots->count() == 0) {
            \Log::error('Ниодного заведения не существует в базе данных');
            throw new ValidationException(['posterError' => "Произошла ошибка, попробуйте позже"]);
        }


        $selectedSpot = $this->getSelectedSpot($spots);

        $posterProducts = new PosterProducts();

        $posterProducts
            ->addCartProducts($products)
            ->addSticks(
                $data['sticks']
            );

        // todo: Доставать имя типа оплаты и типа доставки через базу данных
        // todo: А не через форму
        $poster_comment = PosterUtils::getComment([
            'comment' => $data['comment'] ?? null,
            'change' => $data['change'] ?? null,
            'payment_method_name' => $data['payment'],
            'delivery_method_name' => $data['delivery']
        ]);

        $api = new Api(env('TELEGRAM_BOT_ID'));

        $receipt = new Receipt();
        $receipt->headline("Новый заказ");
        $receipt->make([
            'first_name' => $data['first_name'] ?? null,
            'email' => $data['email'] ?? null,
            'phone' => $data['phone'],
            'address' => $data['address'] ?? null,
            'comment' => $data['comment'] ?? null,
            'payment_method_name' => $data['payment'] ?? null,
            'delivery_method_name' => $data['delivery'] ?? null,
            'change' => $data['change'] ?? null,
            'spot_name' => $selectedSpot->name,
            'products' => $posterProducts->all(),
            'total' => PriceUtils::formattedCartTotal($this->cart)
        ])->newLine()
            ->p('Заказ сделан через сайт');

        $api->sendMessage([
            'text' => $receipt->getText(),
            'parse_mode' => "html",
            'chat_id' => $selectedSpot->getChatId()
        ]);

        PosterApi::init();
        $result = (object)PosterApi::incomingOrders()
            ->createIncomingOrder([
                'spot_id' => $selectedSpot->getTabletId(),
                'phone' => $data['phone'],
                'address' => $data['address'] ?? "",
                'comment' => $poster_comment,
                'products' => $posterProducts->all(),
                'first_name' => $data['first_name'] ?? "",
            ]);


        if (isset($result->error)) {
            $this->onPosterError($result);
            return;
        }

        return $this->onPosterSuccess($data['activeLocale']);
    }

    public function onPosterSuccess($activeLocale): \Illuminate\Http\RedirectResponse
    {
        $this->cart->delete();
        return Redirect::to($activeLocale . '/checkout/done');
    }

    public function onPosterError($result): void
    {
        Log::error("Ошибка при оформлении заказа" . $result->message);
        throw new ValidationException(['posterError' => $result->message]);
    }

    public function getSelectedSpot($spots)
    {
        $session = new Session();
        $spot_id = intval($session->get('activeSpotId'));

        foreach ($spots as $spot) {
            if ($spot_id == $spot['id']) {
                return $spot;
            }
        }
        // По умолчанию будет выбрано первое заведение
        return $spots->first();
    }



}
