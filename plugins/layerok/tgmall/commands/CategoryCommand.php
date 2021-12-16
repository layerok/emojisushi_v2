<?php namespace Layerok\TgMall\Commands;

use Layerok\TgMall\Classes\Callback\Constants;
use Layerok\TgMall\Classes\InlineKeyboard;
use OFFLINE\Mall\Models\Cart;
use OFFLINE\Mall\Models\CartProduct;
use OFFLINE\Mall\Models\Category;
use OFFLINE\Mall\Models\Customer;
use \Telegram\Bot\Commands\Command;
use \Layerok\TgMall\Traits\Lang;
use \Layerok\TgMall\Traits\Warn;
use \Layerok\TgMall\Models\DeleteMessage;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryCommand extends Command
{
    use Lang;
    use Warn;

    protected $name = "category";

    protected $description = "Select products by category id";

    protected $pattern = "{id} {page?}";

    private $brokenImageFileId = "AgACAgQAAxkDAAIBGGGtGjcxSQraUNasYICGA2UkTLeOAAJyrTEbmQABbVHg3HGg2xXRvQEAAwIAA3gAAyIE";

    private $page = 1;
    private $id;



    public function validate():bool
    {

        if (!isset($this->arguments['id'])) {
            $msg = 'Provide unique identifier of the category';
            $this->warn($msg);
            return false;
        }

        if (isset($this->arguments['page'])) {
            $this->page = $this->arguments['page'];
            if (is_numeric($this->page)) {
                if (intval($this->page) < 1) {
                    $msg = 'Page of the category cannot be less than 1';
                    $this->warn($msg);
                    return false;
                }
            } else {
                $msg = 'Page of the category must be number';
                $this->warn($msg);
                return false;
            }
        }
        return true;
    }

    public function handle()
    {
        if(env('TERMINATE_TELEGRAM_COMMANDS')) {
            return;
        };
        $valid = $this->validate();

        if (!$valid) {
            return;
        }

        $this->id = $this->arguments['id'];

        $category = Category::where('id', '=', $this->id)
            ->first();

        if (!$category->exists()) {
            \Log::warning('Category with id [' . $this->id . '] is not found');
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



        $productsInCategory = $category
            ->products()
            ->offset($offset)
            ->limit($countPosition)
            ->get();

        if (isset($this->arguments['page'])) {
            $messages = DeleteMessage::where('chat_id', '=', $chat->id)
                ->latest()
                ->get();
            if($messages->count() > 0) {
                \Telegram::deleteMessage([
                    'chat_id' => $chat->id,
                    'message_id' => $messages->first()->msg_id
                ]);

                DeleteMessage::truncate();
            }
        }

        $productsInCategory->map(
            function (
                $product,
                $index
            ) use (
                $cart,
                $productsInCategory,
                $countPositionInOrder,
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
                    'callback_data' => "/update_qty {$product->id} 1"
                ]);

                $btnCenter = $k::inlineButton([
                    'text' => ($cart->isInCart($product) ? $cartProduct->quantity: '1') . "/10",
                    'callback_data' => 'count_form'
                ]);

                $btnRight = $k::inlineButton([
                    'text' => $this->lang('plus'),
                    'callback_data' =>  "/update_qty {$product->id} 2"
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
                            $product->price()->toArray()['price_formatted'],
                            $this->lang('in_basket_button_title')
                        ),
                        'callback_data' => collect([
                            "tag" => "add_in_basket",
                            "count" => 1,
                            "position_id" => $product->id
                        ])->toJson()
                    ]));
                }


                if (is_null($product->image)) {
                    $photoIdOrUrl = $this->brokenImageFileId;
                } else {
                    $photoIdOrUrl = is_null($product->image->file_id) ?
                        \Telegram\Bot\FileUpload\InputFile::create($product->image->path) : $product->image->file_id;
                }

                $caption = "<b>" . $product->name . "</b>\n\n" . \Html::strip($product->description);
                $response = $this->replyWithPhoto([
                    'photo' => $photoIdOrUrl,
                    'caption' => $caption,
                    'reply_markup' => $k->toJson(),
                    'parse_mode' => 'html'
                ]);

                $photoObject = $response->getPhoto();



                if ($photoObject) {
                    if (!is_null($product->image) && is_null($product->image->file_id)) {
                        $product->image->file_id = $photoObject->first()['file_id'];
                        $product->image->save();
                    }
                }
            }
        ); // end of map

        $all = Category::where('id', '=', $this->id)->first()->products;
        $lastPage = ceil($all->count() / $limit);
        $k = new Keyboard();
        $k->inline();
        if ($lastPage !== $this->page) {
            $loadBtn = $k::inlineButton([
                'text' => 'Загрузить еще из этой категории',
                'callback_data' => implode(' ', ['/category', $this->id, $this->page + 1])
            ]);
            $k->row($loadBtn);
        }

        $btn1 = $k::inlineButton([
            'text' => $this->lang("busket") . $countPositionInOrder,
            'callback_data' => Constants::LOAD_BASKET
        ]);
        $btn2 = $k::inlineButton([
            'text' => $this->lang("in_menu"),
            'callback_data' => "/menu"
        ]);
        $btn3 = $k::inlineButton([
            'text' => $this->lang("in_menu_main"),
            'callback_data' => "/start"
        ]);

        $k->row($btn1);
        $k->row($btn2);
        $k->row($btn3);

        $message = $this->replyWithMessage([
            'text' => $this->lang("triple_dot"),
            'reply_markup' => $k->toJson()
        ]);

        \Log::debug('message');
        \Log::debug($message);
        \Log::debug('message id');
        \Log::debug($message->messageId);

        DeleteMessage::create([
            'chat_id' => $chat->id,
            'msg_id' => $message->messageId
        ]);

    }
}
