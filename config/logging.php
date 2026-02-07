<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Processor\PsrLogMessageProcessor;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that is utilized to write
    | messages to your logs. The value provided here should match one of
    | the channels present in the list of "channels" configured below.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This allows you to get
    | your application ready for upcoming major versions of dependencies.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => env('LOG_DEPRECATIONS_TRACE', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Laravel
    | utilizes the Monolog PHP logging library, which includes a variety
    | of powerful log handlers and formatters that you're free to use.
    |
    | Available drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog", "custom", "stack"
    |
    */

    'channels' => [

        'stack' => [
            'driver' => 'stack',
            'channels' => explode(',', env('LOG_STACK', 'single')),
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => env('LOG_DAILY_DAYS', 14),
            'replace_placeholders' => true,
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => env('LOG_SLACK_USERNAME', 'Laravel Log'),
            'emoji' => env('LOG_SLACK_EMOJI', ':boom:'),
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
        ],

        'papertrail' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
            'handler_with' => [
                'host' => env('PAPERTRAIL_URL'),
                'port' => env('PAPERTRAIL_PORT'),
                'connectionString' => 'tls://' . env('PAPERTRAIL_URL') . ':' . env('PAPERTRAIL_PORT'),
            ],
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'handler_with' => [
                'stream' => 'php://stderr',
            ],
            'formatter' => env('LOG_STDERR_FORMATTER'),
            'processors' => [PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => env('LOG_SYSLOG_FACILITY', LOG_USER),
            'replace_placeholders' => true,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/laravel.log'),
        ],

        'customer_inbound' => [
            'driver' => 'daily',
            'path' => storage_path('logs/customer_inbound/customer_inbound.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'vendor' => [
            'driver' => 'daily',
            'path' => storage_path('logs/vendor/vendor.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'warehouse' => [
            'driver' => 'daily',
            'path' => storage_path('logs/warehouse/warehouse.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'storage' => [
            'driver' => 'daily',
            'path' => storage_path('logs/storage/storage.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inbound_po' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inbound/po/po.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inbound_qc_process' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inbound/qc/qc.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inbound_put_away_process' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inbound/put_away/process.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inbound_put_away_store' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inbound/put_away/store.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inventory_product' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inventory/product.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inventory_change_box' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inventory/change_box.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inventory_change_type' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inventory/change_type.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'inventory_transfer_location' => [
            'driver' => 'daily',
            'path' => storage_path('logs/inventory/transfer_location.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'gr' => [
            'driver' => 'daily',
            'path' => storage_path('logs/gr/gr.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'pm' => [
            'driver' => 'daily',
            'path' => storage_path('logs/pm/pm.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'spare' => [
            'driver' => 'daily',
            'path' => storage_path('logs/spare/spare.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'outbound' => [
            'driver' => 'daily',
            'path' => storage_path('logs/outbound/outbound.log'),
            'level' => 'debug',
            'days' => 14,
        ],

        'user_management' => [
            'driver' => 'daily',
            'path' => storage_path('logs/user_management/user.log'),
            'level' => 'debug',
            'days' => 14,
        ],

    ],

];
