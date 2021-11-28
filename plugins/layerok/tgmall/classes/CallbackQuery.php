<?php
namespace Layerok\TgMall\Classes;
use Illuminate\Support\Facades\Lang;
use Layerok\TgMall\Facades\Telegram;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Customer;
use OFFLINE\Mall\Models\Order;
use OFFLINE\Mall\Models\Product;

class CallbackQuery
{
    public $fns;
    public function __construct()
    {
        $this->fns = new Functions();
    }

    public function handle($responseData)
    {

        $chatId = $responseData->callback_query->from->id;
        $messageId = $responseData->callback_query->message->message_id;
        $callback = json_decode($responseData->callback_query->data);


        $config = [
            "productsInPage" => 20
        ];

        if ($callback->tag == "select_category") {
            $categoryId = $callback->category_id;
            $page = $callback->page;
            $user = Customer::where('tg_chat_id', '=', $chatId)->first();

            $cart = Cart::byUser($user);

            $countPositionInOrder = "";
            if ($cart->products->count()) {
                $countPositionInOrder = " (" . $cart->products->count() . ")";
            }
            $offset = ($page - 1) * $config["productsInPage"];
            $countPosition = $config["productsInPage"];

            $productsInCategory = Category::where('id', '=', $categoryId)
                ->offset($offset)
                ->limit($countPosition)
                ->get();


            $sendBasket = false;
            $productsInCategory->map(
                function (
                    $product,
                    $index
                ) use (
                    $cart,
                    $productsInCategory,
                    $categoryId,
                    $config,
                    $page,
                    $countPositionInOrder,
                    $chatId
                ) {
                    //                $isFileId = false;
//                if($this->fns->getExtension($row["cover"]) == "jpg") {
//                    $photo =  $config["directory"] . "admin/actions/temp-img/" . $row["cover"];
//                } else {
//                    $photo = $row["cover"];
//                    $isFileId = true;
//                }
                    $k = new InlineKeyboard();

                    if ($cart->isInCart($product)) {
                        $printMainButtons = true;
                    } else {
                        $printMainButtons = false;
                    }

                    $cartProduct = CartProduct::where([
                        ['product_id', '=', $product->id],
                        ['cart_id', '=', $cart->id]
                    ])->first();

                    if ($printMainButtons) {
                        $k->addButton(
                            1,
                            Lang::get('layerok.tgmall::telegram.minus'),
                            [
                                "tag" => "position_count",
                                "position_id" =>$product->product_id,
                                "count" => 1
                            ]
                        );
                        $k->addButton(
                            1,
                            $cartProduct->quantity . "/10",
                            "count_form"
                        );
                        $k->addButton(
                            1,
                            Lang::get('layerok.tgmall::telegram.plus'),
                            ["tag" => "position_count" , "position_id" => $product->product_id, "count" => 2]
                        );
                        $k->addButton(
                            2,
                            Lang::get('layerok.tgmall::telegram.position_in_basket'),
                            "position_in_basket"
                        );
                    }
                    else {
                        $k->addButton(
                            1,
                            Lang::get('layerok.tgmall::telegram.minus'),
                            ["tag" => "position_count" , "position_id" => $product->id, "count" => 1]
                        );
                        $k->addButton(
                            1,
                            "1/10",
                            "count_form"
                        );
                        $k->addButton(
                            1,
                            Lang::get('layerok.tgmall::telegram.plus'),
                            ["tag" => "position_count" , "position_id" => $product->product_id, "count" => 2]
                        );
                        $k->addButton(
                            2,
                            str_replace(
                                "*price*",
                                $product->price()->price,
                                Lang::get('layerok.tgmall::telegram.in_basket_button_title')
                            ),
                            ["tag" => "add_in_basket", "count" => 1, "position_id" => $product->product_id]
                        );
                    }

                    if ($index == $productsInCategory->count()) {
                        $all = Category::where('id', '=', $categoryId)->products;
                        $lastPage = ceil($all->count() / $config['productsInPage']);
                        if (!$lastPage == $page) {
                            $k->addButton(
                                3,
                                Lang::get('layerok.tgmall::telegram.load_other_position'),
                                ["tag" => "select_category", "category_id" => $categoryId, "page" => $page + 1]
                            );
                        }

                        $sendBasket = true;
                    }
                    //$photoData = json_decode(Telegram::sendPhoto($chatId, $photo, "<b>" . $row["title"] . "</b>\n\n" . $row["description"], $k->printInlineKeyboard()));

                    if ($sendBasket) {
                        $key = new InlineKeyboard();
                        $key->addButton(
                            1,
                            Lang::get("layerok.tgmall::telegram.busket") . $countPositionInOrder,
                            "load_basket"
                        );
                        $key->addButton(
                            2,
                            Lang::get("layerok.tgmall::telegram.in_menu"),
                            "in_menu"
                        );
                        $key->addButton(
                            3,
                            Lang::get("layerok.tgmall::telegram.in_menu_main"),
                            "in_menu_main"
                        );
//                        $basketMsgId = json_decode(
//                            Telegram::sendMessage(
//                                $chatId,
//                                Lang::get("layerok.tgmall::telegram.triple_dot"),
//                                $key->printInlineKeyboard()
//                            )
//                        )->result->message_id;
//
//
//                        if(!$sqli->inDatabase("basket_message", "`chat_id` = $chatId")) {
//                            $msgid = [
//                                "message_id" => $basketMsgId,
//                                "chat_id" => $chatId
//                            ];
//                            $sqli->insertData("basket_message", $msgid);
//
//                        } else {
//                            $msgid = [
//                                "message_id" => $basketMsgId,
//                            ];
//                            $sqli->updateData("basket_message", $msgid, "`chat_id` = $chatId");
//                        }
                    }

//                if(!$isFileId) {
//                    $sqli->updateData("positions", ["cover" => max($photoData->result->photo)->file_id], "`id` = " . $row["id"]);
//                    if (file_exists("admin/actions/temp-img/" . $row["cover"])) {
//                        unlink("admin/actions/temp-img/" . $row["cover"]);
//                    }
//                }
                });

        }
    }
}
