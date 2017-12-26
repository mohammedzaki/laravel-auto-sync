<?php

/**
 * This file is part of laravel-auto-sync,
 * an automatic synchronize MySql database changes on remote locations for laravel applications (Multi slave servers to one Master server replication).
 *
 * @license MIT
 * @package mohammed-zaki/laravel-auto-sync
 */
return [
    'server'      => [
        'id'   => env('AUTO_SYNC_SERVER_ID', 00),
        'name' => env('AUTO_SYNC_SERVER_NAME', 'server-name'),
    ],
    'main_folder' => storage_path('sync'),
    'channel'     => config('autosync.server.name') . '-' . config('autosync.server.id'),
    'folders'     => [
        'current_logger'  => 'logger',
        'current_syncing' => 'syncing',
        'synced'          => 'synced'
    ],
    'file'        => [
        'prefix'        => 'bin',
        'current_state' => 'current'
    ],
];
