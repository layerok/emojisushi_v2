<?php namespace Layerok\TgMall\Classes\Callback;

use Layerok\TgMall\Classes\InlineKeyboard;
use Layerok\TgMall\Facades\Telegram;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Category;
use Config;
use OFFLINE\Mall\Models\Customer;

class ShowProductsByCategory implements Action
{
    use \Layerok\TgMall\Traits\Lang;
    public $callback;
    public $chatId;
    private $brokenImageFileId = "AgACAgQAAxkDAAIBGGGtGjcxSQraUNasYICGA2UkTLeOAAJyrTEbmQABbVHg3HGg2xXRvQEAAwIAA3gAAyIE";



    public function run()
    {
        $this->chatId = Telegram::getChatId();
        $limit = \Config::get('layerok.tgmall::productsInPage');
        \Log::info('products in one page ' . $limit);
        $categoryId = $this->callback->category_id;
        $page = $this->callback->page;
        $customer = Customer::where('tg_chat_id', '=', $this->chatId)->first();


        $cart = Cart::byUser($customer->user);

        $countPositionInOrder = "";
        if ($cart->products->count()) {
            $countPositionInOrder = " (" . $cart->products->count() . ")";
        }
        $offset = ($page - 1) * $limit;
        $countPosition = $limit;


        $productsInCategory = Category::where('id', '=', $categoryId)
            ->first()
            ->products()
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
                $page,
                $countPositionInOrder,
                $sendBasket,
                $limit
            ) {

                $k = new InlineKeyboard();

                $cartProduct = CartProduct::where([
                    ['product_id', '=', $product->id],
                    ['cart_id', '=', $cart->id]
                ])->first();

                $k->addButton(
                    1,
                    $this->lang('minus'),
                    [
                        "tag" => "position_count",
                        "position_id" => $product->product_id,
                        "count" => 1
                    ]
                );
                $k->addButton(
                    1,
                    ($cart->isInCart($product) ? $cartProduct->quantity: '1') . "/10",
                    "count_form"
                );
                $k->addButton(
                    1,
                    $this->lang('plus'),
                    ["tag" => "position_count", "position_id" => $product->id, "count" => 2]
                );


                if ($cart->isInCart($product)) {
                    $k->addButton(
                        2,
                        $this->lang('position_in_basket'),
                        "position_in_basket"
                    );
                } else {
                    $k->addButton(
                        2,
                        str_replace(
                            "*price*",
                            $product->price()->price,
                            $this->lang('in_basket_button_title')
                        ),
                        ["tag" => "add_in_basket", "count" => 1, "position_id" => $product->id]
                    );
                }

                \Log::info('index ' . $index);
                \Log::info('count ' . $productsInCategory->count());

                if ($index == $productsInCategory->count() - 1) {
                    $all = Category::where('id', '=', $categoryId)->first()->products;
                    $lastPage = ceil($all->count() / $limit);
                    if ($lastPage !== $page) {
                        $k->addButton(
                            3,
                            $this->lang('load_other_position'),
                            ["tag" => "select_category", "category_id" => $categoryId, "page" => $page + 1]
                        );
                    }

                    $sendBasket = true;
                }

                if (is_null($product->image)) {
                    $photoIdOrUrl = $this->brokenImageFileId;
                } else {
                    $photoIdOrUrl = is_null($product->image->file_id) ? $product->image->path : $product->image->file_id;
                }

                $caption = "<b>" . $product->name . "</b>\n\n" . \Html::strip($product->description);
                $photoData = json_decode(
                    Telegram::sendPhoto(
                        $photoIdOrUrl,
                        $caption,
                        $k->printInlineKeyboard()
                    )
                );

                if ($photoData->ok) {
                    if (!is_null($product->image) && is_null($product->image->file_id)) {
                        $product->image->file_id = max($photoData->result->photo)->file_id;
                        $product->image->save();
                    }
                }


                if ($sendBasket) {
                    $key = new InlineKeyboard();
                    $key->addButton(
                        1,
                        $this->lang("busket") . $countPositionInOrder,
                        Constants::LOAD_BASKET
                    );
                    $key->addButton(
                        2,
                        $this->lang("in_menu"),
                        Constants::SHOW_MENU
                    );
                    $key->addButton(
                        3,
                        $this->lang("in_menu_main"),
                        Constants::GO_TO_MAIN_MENU
                    );
                    Telegram::sendMessage(
                        $this->lang("triple_dot"),
                        $key->printInlineKeyboard()
                    );

//                        $basketMsgId = json_decode(
//                            Telegram::sendMessage(
//                                $chatId,
//                                $this->lang("triple_dot"),
//                                $key->printInlineKeyboard()
//                            )
//                        )->result->message_id;

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

            }
        );
    }
}
