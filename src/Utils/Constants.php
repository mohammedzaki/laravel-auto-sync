<?php

/*
 * The MIT License
 *
 * Copyright 2017 Mohammed Zaki mohammedzaki.dev@gmail.com.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

namespace AutoSync\Utils;

/**
 * Description of Constants
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class Constants {

    const MAIN_FOLDER               = 'autosync.main_folder';
    const SERVER_ID                 = 'autosync.server.id';
    const SERVER_NAME               = 'autosync.server.name';
    const SERVER_IS_MASTER          = 'autosync.server.is_master';
    const MASTER_SERVER_URL         = 'autosync.master_server.url';
    const MASTER_SERVER_USERNAME    = 'autosync.master_server.username';
    const MASTER_SERVER_PASSWORD    = 'autosync.master_server.password';
    const MASTER_SERVER_SYNC_API    = 'autosync.master_server.sync_api_name';
    const CURRENT_LOGGER_FOLDER     = 'autosync.folders.current_logger';
    const CURRENT_SYNCING_FOLDER    = 'autosync.folders.current_syncing';
    const SYNCED_FOLDER             = 'autosync.folders.synced';
    const FILE_PREFIX               = 'autosync.file.prefix';
    const FILE_CURRENT_STATE        = 'autosync.file.current_state';
    const MAX_RECORDS               = 'autosync.file.max_records';
    const IGNORED_TABLES            = 'autosync.ignored_tables';
    const SYNC_SCHEDULE_TIME        = 'autosync.sync_schedule_time';
    const SYNC_QUEUE_NAME           = 'autosync.sync_queue.name';
    const SYNC_QUEUE_DRIVER         = 'autosync.sync_queue.driver';
    const SYNC_QUEUE_DELAY          = 'autosync.sync_queue.delay';
    const CHANNEL                   = 'autosync.channel';
    const CURRENT_FILE_INDEX        = 'current_index';
    const CURRENT_SYNCING_FILE      = 'current_syncing';
    const CURRENT_LOG_RECORD        = 'current_record';
    const ENV_AUTO_SYNC_SERVER_ID   = 'AUTO_SYNC_SERVER_ID';
    const ENV_AUTO_SYNC_SERVER_NAME = 'AUTO_SYNC_SERVER_NAME';
    const API_LOG_FILE              = 'logFile';

}
