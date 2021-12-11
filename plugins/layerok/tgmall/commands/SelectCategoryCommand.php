<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Callback\Constants;
use Layerok\TgMall\Classes\InlineKeyboard;
use Layerok\TgMall\Facades\Telegram;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Customer;
use \Telegram\Bot\Commands\Command;
use \Layerok\TgMall\Traits\Lang;
use Telegram\Bot\Keyboard\Keyboard;

class SelectCategoryCommand extends Command
{
    use Lang;

    protected $name = "selectcategory";

    protected $description = "Select products by category id";

    protected $pattern = "{id} {page?}";

    private $brokenImageFileId = "AgACAgQAAxkDAAIBGGGtGjcxSQraUNasYICGA2UkTLeOAAJyrTEbmQABbVHg3HGg2xXRvQEAAwIAA3gAAyIE";

    private $page = 1;
    private $id;

    public function handle()
    {
        return;
        if (!isset($this->arguments['id'])) {
            \Log::error('category id is not set');
        }

        $this->id = $this->arguments['id'];

        if (isset($this->arguments['page'])) {
            $this->page = $this->arguments['page'];
        }

        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();


        $limit = \Config::get('layerok.tgmall::productsInPage');

        $customer = Customer::where('tg_chat_id', '=', $chat->id)->first();

        $cart = Cart::byUser($customer->user);

        $countPositionInOrder = "";
        if ($cart->products->count()) {
            $countPositionInOrder = " (" . $cart->products->count() . ")";
        }
        $offset = ($this->page - 1) * $limit;
        $countPosition = $limit;

        $category = Category::where('id', '=', $this->id)
            ->first();

        if (!$category->exists()) {
           \Log::error('Category with id [' . $this->id . '] is not found');
        }

        $productsInCategory = $category
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
                $countPositionInOrder,
                $sendBasket,
                $limit
            ) {

                $k = new Keyboard();
                $k->inline();

                $cartProduct = CartProduct::where([
                    ['product_id', '=', $product->id],
                    ['cart_id', '=', $cart->id]
                ])->first();

                $btnLeft = $k::inlineButton([
                    'text' => $this->lang('minus'),
                    'callback_data' => collect([
                        "tag" => "position_count",
                        "position_id" => $product->product_id,
                        "count" => 1
                    ])->toJson()
                ]);

                $btnCenter = $k::inlineButton([
                    'text' => ($cart->isInCart($product) ? $cartProduct->quantity: '1') . "/10",
                    'callback_data' => 'count_form'
                ]);

                $btnRight = $k::inlineButton([
                    'text' => $this->lang('plus'),
                    'callback_data' =>  collect([
                        "tag" => "position_count",
                        "position_id" => $product->id,
                        "count" => 2
                    ])->toJson()
                ]);


                $k->row($btnLeft, $btnCenter, $btnRight);


                if ($cart->isInCart($product)) {

                    $k->row($k::inlineButton([
                        'text' => $this->lang('position_in_basket'),
                        'callback_data' => "position_in_basket"
                    ]));

                } else {
                    $k->row($k::inlineButton([
                        'text' => str_replace(
                            "*price*",
                            $product->price()->price,
                            $this->lang('in_basket_button_title')
                        ),
                        'callback_data' => collect([
                            "tag" => "add_in_basket",
                            "count" => 1,
                            "position_id" => $product->id
                        ])->toJson()
                    ]));
                }

                \Log::info('index ' . $index);
                \Log::info('count ' . $productsInCategory->count());

                if ($index == $productsInCategory->count() - 1) {
                    $all = Category::where('id', '=', $this->id)->first()->products;
                    $lastPage = ceil($all->count() / $limit);
                    if ($lastPage !== $this->page) {
                        $k->row($k::inlineButton([
                            'text' => $this->lang('load_other_position'),
                            'callback_data' => collect([
                                "tag" => "select_category",
                                "category_id" => $this->id,
                                "page" => $this->page + 1
                            ])->toJson()
                        ]));
                    }

                    $sendBasket = true;
                }

                if (is_null($product->image)) {
                    $photoIdOrUrl = $this->brokenImageFileId;
                } else {
                    $photoIdOrUrl = is_null($product->image->file_id) ? $product->image->path : $product->image->file_id;
                }

                $caption = "<b>" . $product->name . "</b>\n\n" . \Html::strip($product->description);
                $photoData = $this->replyWithPhoto([
                        'photo' => $photoIdOrUrl,
                        'caption' => $caption,
                        'reply_markup' => $k->toJson()
                    ]);




                if ($photoData->ok) {
                    if (!is_null($product->image) && is_null($product->image->file_id)) {
                        $product->image->file_id = max($photoData->result->photo)->file_id;
                        $product->image->save();
                    }
                }


                if ($sendBasket) {
                    $k = new Keyboard();
                    $btn1 = $k::inlineButton([
                        'text' =>  $this->lang("busket") . $countPositionInOrder,
                        'callback_data' => Constants::LOAD_BASKET
                    ]);
                    $btn2 = $k::inlineButton([
                        'text' => $this->lang("in_menu"),
                        'callback_data' => Constants::SHOW_MENU
                    ]);
                    $btn3 = $k::inlineButton([
                        'text' => $this->lang("in_menu_main"),
                        'callback_data' => Constants::GO_TO_MAIN_MENU
                    ]);

                    $k->row($btn1);
                    $k->row($btn2);
                    $k->row($btn3);

                    $this->replyWithMessage([
                        'text' => $this->lang("triple_dot"),
                        'reply_markup' => $k->toJson()
                    ]);


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
