<?php

/**
 * This file is part of laravel-auto-sync,
 * an automatic synchronize MySql database changes on remote locations for laravel applications (Multi slave servers to one Master server replication).
 *
 * @license MIT
 * @package mohammed-zaki/laravel-auto-sync
 */

return [
    'server'    => [
        'id'   => 01,
        'name' => 'server_name',
    ],
    'main_folder' => storage_path('sync'),
    'channel'     => 'hospital',
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
