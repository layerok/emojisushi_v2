<?php

namespace Layerok\TgMall\Classes;

use Layerok\TgMall\Facades\Telegram;
use Layerok\TgMall\Models\Admin;
use Layerok\TgMall\Models\Contact;
use Illuminate\Support\Facades\Lang;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\User;

class Message
{

    public $fns;

    public function __construct()
    {
        $this->fns = new Functions();
    }

    public function handle($responseData)
    {

        $chatId = $responseData->message->chat->id;
        $message = $responseData->message->text;
        $firstName = $responseData->message->from->first_name;
        $username = $responseData->message->from->username;
        \Log::info('got message: ' . $message);
        $admin = Admin::where('chat_id', '=', $chatId)->first();

//        $file_id = max($responseData->message->photo)->file_id;
//
//        if ($file_id != "" && $admin) {
//            Telegram::sendMessage($chatId, $file_id);
//            exit();
//        } elseif ($responseData->message->animation->file_id != "") {
//            Telegram::sendMessage($chatId, $responseData->message->animation->file_id);
//            exit();
//        }

//        if ($message == "/test") {
//            $photo = "https://webmaster-shulyak.ru/works/test-shop/admin/actions/temp-img/test.jpg";
//
//            $sf = Telegram::sendPhoto($chatId, $photo);
//
//            Telegram::sendMessage($chatId, $sf);
//        }
//
        if ($message == "/photo") {
            $photo = "https://emojisushi.com.ua/storage/app/media/broken.png";


            $sf = Telegram::sendPhoto($photo);


            Telegram::sendMessage($sf);
        }

        if ($message == "/start") {
            $customer = Customer::where('tg_chat_id', '=', $chatId)->first();
            \Log::info('customer found: ' . json_encode($customer));
            if (!$customer) {
                $pass = "qweasdqweasd";
                $user = User::create([
                    'name' => "jonh",
                    'password' => $pass,
                    'password_confirmation' => $pass
                ]);
                Customer::create([
                    "tg_chat_id" => $chatId,
                    "firstname" => $firstName,
                    "lastname"  => "(empty)",
                    "tg_username" => $username,
                    "user_id" => $user->id
                ]);
            }


            $this->fns->sendMainPanel1($chatId, $firstName);
        }
        elseif ($message == Lang::get('layerok.tgmall::telegram.review')) {
            $z = 1;
            $k = new InlineKeyboard();

            $points = [
                ["id" => 1, "title" => "Небесной сотни"],
                ["id" => 2, "title" => "Торговая"],
            ];

            foreach ($points as $row) {
                $k->addButton($z, $row["title"], ["tag" => "add_review", "point_id" => $row["id"]]);
                $z++;
            }

            Telegram::sendMessage(
                Lang::get('layerok.tgmall::telegram.ask_review'),
                $k->printInlineKeyboard()
            );
        } elseif ($message == Lang::get('layerok.tgmall::telegram.menu')) {
            $this->fns->printMainMenu($chatId);
        } elseif ($message == Lang::get('layerok.tgmall::telegram.contact')) {
            Telegram::sendMessage(
                Lang::get('layerok.tgmall::telegram.zavernuli_contact')
            );
        } elseif ($message == Lang::get('layerok.tgmall::telegram.delivery_and_pay')) {
            Telegram::sendMessage(
                Lang::get('layerok.tgmall::telegram.delivery_and_pay_text')
            );
        } elseif ($message == Lang::get('layerok.tgmall::telegram.my_order')) {
            $k = new InlineKeyboard();
            $z = 1;

            $orders = [
                ["id" => 1],
                ["id" => 2],
                ["id" => 3],
            ];

            foreach ($orders as $row) {
                $k->addButton(
                    $z,
                    $row["date"],
                    ["tag" => "load_old_order", "id" => $row["id"]]
                );
                $z++;
            }
            Telegram::sendMessage(
                Lang::get('layerok.tgmall::telegram.old_order'),
                $k->printInlineKeyboard()
            );

        } elseif ($message == Lang::get('layerok.tgmall::telegram.busket')) {
            $this->fns->loadBasket($chatId);
        }
    }
}
