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

namespace AutoSync\Filesystem;

use File;

/**
 * Description of Helpers
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class Helpers {

    const MAIN_FOLDER            = 'autosync.main_folder';
    const CURRENT_LOGGER_FOLDER  = 'autosync.folders.current_logger';
    const CURRENT_SYNCING_FOLDER = 'autosync.folders.current_syncing';
    const SYNCED_FOLDER          = 'autosync.folders.synced';
    const FILE_PREFIX            = 'autosync.file.prefix';
    const FILE_CURRENT_STATE     = 'autosync.file.current_state';
    const CURRENT_INDEX          = 'current_index';
    const CURRENT_SYNCING        = 'current_syncing';
    const CURRENT_RECORD         = 'current_record';

    static function getMainDirectory()
    {
        return config(static::MAIN_FOLDER);
    }

    static function getLoggerDirectory()
    {
        $directory = static::getMainDirectory() . '/' . config(static::CURRENT_LOGGER_FOLDER);
        return $directory;
    }

    static function getCurrentSyncingDirectory()
    {
        $directory = static::getMainDirectory() . '/' . config(static::CURRENT_SYNCING_FOLDER);
        return $directory;
    }

    static function getSyncedDirectory()
    {
        $directory = static::getMainDirectory() . '/' . config(static::SYNCED_FOLDER);
        return $directory;
    }

    static function getCurrentLogName()
    {
        $fileName = config(static::FILE_PREFIX) . '_' . static::getCurrentLogState(self::CURRENT_INDEX) . '.log';
        return $fileName;
    }

    static function getCurrentLogFilePath()
    {
        $path = Helpers::getLoggerDirectory() . '/' . Helpers::getCurrentLogName();
        return $path;
    }

    static function getCurrentLogStateFile()
    {
        $path = static::getMainDirectory() . '/' . config(static::FILE_CURRENT_STATE) . '.json';
        return $path;
    }

    static function setCurrentLogState($key, $newValue)
    {
        $path    = static::getCurrentLogStateFile();
        $content = File::get($path);
        $data    = collect(json_decode($content));
        $data->offsetSet($key, $newValue);
        File::put($path, json_encode($data));
    }

    static function getCurrentLogState($key)
    {
        $path    = static::getCurrentLogStateFile();
        $content = File::get($path);
        $data    = collect(json_decode($content));
        return $data->get($key);
    }

}
