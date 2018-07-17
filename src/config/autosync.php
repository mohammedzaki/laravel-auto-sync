<?php

/**
 * This file is part of laravel-auto-sync,
 * an automatic synchronize MySql database changes on remote locations for laravel applications (Multi slave servers to one Master server replication).
 *
 * @license MIT
 * @package mohammed-zaki/laravel-auto-sync
 */
return [
    'server'             => [
        'id'        => env('AUTO_SYNC_SERVER_ID', 00),
        'name'      => env('AUTO_SYNC_SERVER_NAME', 'server-name'),
        'is_master' => env('AUTO_SYNC_IS_MASTER_SERVER', false),
    ],
    'master_server'      => [
        'url'           => env('AUTO_SYNC_MASTER_SERVER_URL', ''),
        'username'      => env('AUTO_SYNC_MASTER_SERVER_USERNAME', ''),
        'password'      => env('AUTO_SYNC_MASTER_SERVER_PASSWORD', ''),
        'sync_api_name' => env('AUTO_SYNC_API_NAME', '/api/autosync/pushNewSyncFile'),
    ],
    'main_folder'        => env('AUTO_SYNC_MAIN_FOLDER', storage_path('sync')),
    'channel'            => env('AUTO_SYNC_SERVER_NAME', 'server-name') . '-' . env('AUTO_SYNC_SERVER_ID', 00),
    'folders'            => [
        'current_logger'  => env('AUTO_SYNC_CURRENT_LOGGER_FOLDER', 'logger'),
        'current_syncing' => env('AUTO_SYNC_CURRENT_SYNCING_FOLDER', 'syncing'),
        'synced'          => env('AUTO_SYNC_SYNCED_FILES_FOLDER', 'synced')
    ],
    'sync_schedule_time' => env('AUTO_SYNC_SCHEDULE_TIME', '*/2 * * * * *'),
    'sync_queue'         => [
        'name'   => env('AUTO_SYNC_QUEUE_NAME', 'AutoSyncProcessing'),
        'driver' => env('AUTO_SYNC_QUEUE_DRIVER', 'database'),
        'delay'  => env('AUTO_SYNC_QUEUE_DELAY', 5),
    ],
    'file'               => [
        'prefix'        => env('AUTO_SYNC_FILE_PREFIX', 'bin'),
        'current_state' => env('AUTO_SYNC_CURRENT_STATE_FILENAME', 'current'),
        'max_records'   => env('AUTO_SYNC_MAX_RECORDS', '10'),
    ],
    'ignored_tables'     => [
        '`oauth_access_tokens`',
        '`oauth_auth_codes`',
        '`oauth_clients`',
        '`oauth_personal_access_clients`',
        '`oauth_refresh_tokens`',
        '`password_resets`',
        '`migrations`',
        '`permissions`',
        '`roles`',
        '`permission_role`',
        '`role_user`',
        '`jobs`',
        '`users`',
        '`notifications`'
    ]
];
