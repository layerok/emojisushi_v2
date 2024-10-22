<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'single'),

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['daily', 'telegram'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/system.log'),
            'level' => 'debug',
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/system.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'October CMS Log',
            'emoji' => ':boom:',
            'level' => 'critical',
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => 'debug',
            'handler' => \Monolog\Handler\SyslogUdpHandler::class,
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
            ],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'handler' => \Monolog\Handler\StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'with' => [
                'stream' => 'php://stderr',
            ],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => 'debug',
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => 'debug',
        ],

        'mail'  => [
            'driver' => 'monolog',
            'level' => 'error',
            'handler' => \Monolog\Handler\NativeMailerHandler::class,
            'with' => [
                'to' => env('LOG_ERROR_EMAIL_TO', 'kotopes231@gmail.com'),
                'subject' => env('LOG_ERROR_EMAIL_SUBJECT', '[Error] emojisushi.com.ua'),
                'from'    => env('LOG_ERROR_EMAIL_FROM', 'kotopes231@gmail.com')
            ]
        ],
        'telegram' => [
            'driver' => 'monolog',
            'level'  => 'info',
            'handler' => \Monolog\Handler\TelegramBotHandler::class,
            'with'    => [
                'apiKey' => env('MY_LOG_BOT_TOKEN'),
                'channel' => env('MY_LOG_BOT_CHAT_ID')
            ],
            'tap' => [
                \Layerok\TgMall\Classes\Taps\CustomizeMonologTelegramHandler::class
            ]
        ]
    ],

];
