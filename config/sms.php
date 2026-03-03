<?php

declare(strict_types=1);

return [

    /*
    |--------------------------------------------------------------------------
    | Default SMS Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the default SMS connection that gets used while
    | using this SMS library. This connection is used when another is
    | not explicitly specified when executing a given SMS function.
    |
    */

    'default' => env('SMS_DEFAULT_CHANNEL', 'transactional'),

    /*
    |--------------------------------------------------------------------------
    | SMS Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure all of the SMS "channels" for your application as
    | well as their configuration. You may even configure multiple channels
    | of the same type or using the same parameters.
    |
    */

    'base_settings' => [
        'base_url' => env('SMS_URL'),
        'apikey' => env('SMS_API_KEY'),
        'senderid' => env('SMS_SENDER_ID'),
        'channel' => env('SMS_CHANNEL'),
        'DCS' => env('SMS_DCS', false),
        'flashsms' => env('SMS_FLASH_SMS', false),
        'route' => env('SMS_ROUTE'),
        'EntityId' => env('SMS_ENTITY_ID'),
        'dlttemplateid' => env('SMS_DLT_TEMPLATE_ID'),
    ],

    'channels' => [

        'transactional' => [
            'base_url' => env('SMS_TRANSACTIONAL_URL', env('SMS_URL')),
            'apikey' => env('SMS_TRANSACTIONAL_API_KEY', env('SMS_API_KEY')),
            'senderid' => env('SMS_TRANSACTIONAL_SENDER_ID', env('SMS_SENDER_ID')),
            'channel' => env('SMS_TRANSACTIONAL_CHANNEL', env('SMS_CHANNEL')),
            'DCS' => env('SMS_TRANSACTIONAL_DCS', env('SMS_DCS', false)),
            'flashsms' => env('SMS_TRANSACTIONAL_FLASH_SMS', env('SMS_FLASH_SMS', false)),
            'route' => env('SMS_TRANSACTIONAL_ROUTE', env('SMS_ROUTE')),
            'EntityId' => env('SMS_TRANSACTIONAL_ENTITY_ID', env('SMS_ENTITY_ID')),
            'dlttemplateid' => env('SMS_TRANSACTIONAL_DLT_TEMPLATE_ID', env('SMS_DLT_TEMPLATE_ID')),
        ],

        'marketing' => [
            'base_url' => env('SMS_MARKETING_URL', env('SMS_URL')),
            'apikey' => env('SMS_MARKETING_API_KEY', env('SMS_API_KEY')),
            'senderid' => env('SMS_MARKETING_SENDER_ID', env('SMS_SENDER_ID')),
            'channel' => env('SMS_MARKETING_CHANNEL', env('SMS_CHANNEL')),
            'DCS' => env('SMS_MARKETING_DCS', env('SMS_DCS', false)),
            'flashsms' => env('SMS_MARKETING_FLASH_SMS', env('SMS_FLASH_SMS', false)),
            'route' => env('SMS_MARKETING_ROUTE', env('SMS_ROUTE')),
            'EntityId' => env('SMS_MARKETING_ENTITY_ID', env('SMS_ENTITY_ID')),
            'dlttemplateid' => env('SMS_MARKETING_DLT_TEMPLATE_ID', env('SMS_DLT_TEMPLATE_ID')),
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Send SMS Only In Production
    |--------------------------------------------------------------------------
    |
    | If true, SMS will only be sent when APP_ENV=production; otherwise the
    | request is skipped and logged.
    |
    */

    'send_sms_only_in_production' => env('SEND_SMS_ONLY_IN_PRODUCTION', false),

];
