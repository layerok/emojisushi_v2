<?php namespace Layerok\TgMall\Classes\Callbacks;

use Layerok\TgMall\Classes\Traits\Lang;
use Lovata\BaseCode\Models\HideCategory;
use OFFLINE\Mall\Models\Category;
use Telegram\Bot\Actions;
use Telegram\Bot\Keyboard\Keyboard;

class MenuHandler extends CallbackQueryHandler
{
    use Lang;

    protected $extendMiddlewares = [
        \Layerok\TgMall\Classes\Middleware\CheckNotChosenBranchMiddleware::class
    ];
    public function handle()
    {
        $update = $this->getUpdate();
        $from = $update->getMessage()->getFrom();
        $chat = $update->getChat();


        // This will update the chat status to typing...
        $this->replyWithChatAction(['action' => Actions::TYPING]);
        $keyboard = new Keyboard();
        $keyboard->inline();

        $categories = Category::where([
            ['nest_depth', '=', 0],
            ['published', '=', 1]
        ])->get();
        $categories->map(function ($row) use ($keyboard) {

            $hidden = HideCategory::where([
                ['branch_id', '=', $this->customer->branch->id],
                ['category_id', '=', $row->id]
            ])->exists();
            if ($hidden) {
                return;
            }

            $btn = $keyboard::inlineButton(
                [
                    'text' => $row->name,
                    'callback_data' => json_encode([
                        'name' => 'category',
                        'arguments' => [
                            'id' => $row->id,
                            'page' => 1
                        ]
                    ])
                ]
            );
            $keyboard->row($btn);
        });

        $keyboard->row($keyboard::inlineButton([
            'text' => self::lang('in_menu_main'),
            'callback_data' => json_encode([
                'name' => 'start',
                'arguments' => []
            ])
        ]));

        $replyWith = [
            'text' => self::lang('menu_text'),
            'reply_markup' => $keyboard->toJson()
        ];

        $this->replyWithMessage($replyWith);
    }
}
