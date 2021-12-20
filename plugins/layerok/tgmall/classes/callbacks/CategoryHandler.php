<?php

namespace Layerok\TgMall\Classes\Callbacks;


use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Markups\CategoryFooterReplyMarkup;
use Layerok\TgMall\Classes\Markups\CategoryProductReplyMarkup;
use Layerok\TgMall\Classes\Markups\ProductInCartReplyMarkup;
use Layerok\TgMall\Models\DeleteMessage;
use Layerok\TgMall\Models\Message;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use Layerok\TgMall\Classes\Traits\Before;
use Lovata\BaseCode\Models\HideCategory;
use Lovata\BaseCode\Models\HideProduct;
use OFFLINE\Mall\Models\Category as CategoryModel;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryHandler extends CallbackQueryHandler
{
    use Lang;
    use Warn;
    use Before;

    private $brokenImageFileId = "AgACAgQAAxkDAAIBGGGtGjcxSQraUNasYICGA2UkTLeOAAJyrTEbmQABbVHg3HGg2xXRvQEAAwIAA3gAAyIE";

    private $page = 1;
    private $id;
    public $cart;


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


        $valid = $this->validate();

        if (!$valid) {
            return;
        }
        $this->before();
        $this->id = $this->arguments['id'];

        $category = CategoryModel::where('id', '=', $this->id)
            ->first();

        if (!$category->exists()) {
            \Log::warning('Category with id [' . $this->id . '] is not found');
        }

        $update = $this->getUpdate();
        $chat = $update->getChat();


        $limit = \Config::get('layerok.tgmall::productsInPage');


        $offset = ($this->page - 1) * $limit;
        $countPosition = $limit;


        $hidden = HideCategory::where([
            ['branch_id', '=', $this->customer->branch->id],
            ['category_id', '=', $category->id]
        ])->exists();

        if ($hidden) {
            return;
        }
        $productsInCategory = $category
            ->products()
            ->offset($offset)
            ->limit($countPosition)
            ->get();

        if (isset($this->arguments['page'])) {
            $messages = DeleteMessage::where('chat_id', '=', $chat->id)
                ->latest()
                ->get();
            if ($messages->count() > 0) {
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
                $productsInCategory,
                $limit
            ) {

                $hidden = HideProduct::where([
                    ['branch_id', '=', $this->customer->branch->id],
                    ['product_id', '=', $product->id]
                ])->exists();

                if ($hidden) {
                    return;
                }

                if ($this->cart->isInCart($product)) {
                    $replyMarkup = new ProductInCartReplyMarkup();
                } else {
                    $replyMarkup = new CategoryProductReplyMarkup($product, 1);
                }

                $k = $replyMarkup->getKeyboard();

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

        $k = $this->footerButtons();


        $message = $this->replyWithMessage([
            'text' => $this->lang("triple_dot"),
            'reply_markup' => $k->toJson()
        ]);

        $msg_id = $message->messageId;

        DeleteMessage::create([
            'chat_id' => $chat->id,
            'msg_id' => $msg_id
        ]);

        Message::where('chat_id', '=', $chat->id)
            ->where('type', '=', Constants::UPDATE_CART_TOTAL_IN_CATEGORY)
            ->orWhere('type', '=', Constants::UPDATE_CART_TOTAL)
            ->delete();

        $message = Message::create([
            'chat_id' => $chat->id,
            'msg_id' => $msg_id,
            'type' => Constants::UPDATE_CART_TOTAL_IN_CATEGORY,
            'meta_data' => [
                'category_id' => $this->id,
                'page' => $this->page
            ]
        ]);
    }

    public function footerButtons(): Keyboard
    {
        $replyMarkup = new CategoryFooterReplyMarkup($this->cart, $this->id, $this->page);
        return $replyMarkup->getKeyboard();
    }
}
