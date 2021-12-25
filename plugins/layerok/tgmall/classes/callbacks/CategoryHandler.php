<?php

namespace Layerok\TgMall\Classes\Callbacks;


use Layerok\TgMall\Classes\Constants;
use Layerok\TgMall\Classes\Markups\CategoryFooterReplyMarkup;
use Layerok\TgMall\Classes\Markups\CategoryProductReplyMarkup;
use Layerok\TgMall\Classes\Markups\ProductInCartReplyMarkup;
use Layerok\TgMall\Classes\Utils\Utils;
use Telegram\Bot\FileUpload\InputFile;
use Layerok\TgMall\Classes\Traits\Lang;
use Layerok\TgMall\Classes\Traits\Warn;
use Lovata\BaseCode\Models\HideCategory;
use Lovata\BaseCode\Models\HideProduct;
use OFFLINE\Mall\Models\Category as CategoryModel;
use Telegram\Bot\Keyboard\Keyboard;

class CategoryHandler extends CallbackQueryHandler
{
    use Lang;
    use Warn;

    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckBranchMiddleware::class
    ];

    private $brokenImagePath = "https://emojisushi.com.ua/storage/app/media/broken.png";
    private $brokenFileID = "AgACAgIAAxkDAAP1YcT047ZwFnSayz5O4z8qaMp8GIEAApa5MRurUChKRFXDoi0uwMoBAAMCAANzAAMjBA";

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
            ->where('published', '=', '1')
            ->offset($offset)
            ->limit($countPosition)
            ->get();

        if (isset($this->arguments['page']) && $this->arguments['page'] !== 1) {
            $deleteMsg = $this->state->getDeleteMsgInCategory();
            if ($deleteMsg) {
                try {
                    \Telegram::deleteMessage([
                        'chat_id' => $chat->id,
                        'message_id' => $deleteMsg['id']
                    ]);
                } catch (\Exception $e) {
                    \Log::warning("Caught Exception ('{$e->getMessage()}')\n{$e}\n");
                }
                $this->state->setDeleteMsgInCategory(null);
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

                $caption = Utils::getCaption($product);

                if (!is_null($product->image)) {
                    $photoIdOrUrl = Utils::getPhotoIdOrUrl($product);
                    $response = $this->replyWithPhoto([
                        'photo' => $photoIdOrUrl,
                        'caption' => $caption,
                        'reply_markup' => $k->toJson(),
                        'parse_mode' => 'html',
                    ]);

                    Utils::setFileIdFromResponse($response, $product);
                } else {
                    $this->replyWithMessage([
                        'text' => $caption,
                        'reply_markup' => $k->toJson(),
                        'parse_mode' => 'html',
                    ]);
                }

            }
        ); // end of map

        $k = $this->footerButtons();


        $message = $this->replyWithMessage([
            'text' => $this->lang("triple_dot"),
            'reply_markup' => $k->toJson()
        ]);

        $msg_id = $message->messageId;

        $this->state->setDeleteMsgInCategory(['id' => $msg_id]);

        $this->state->setCartCountMsg([
            'id' => $msg_id,
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
