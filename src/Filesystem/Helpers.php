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
use AutoSync\Filesystem\Constants;
use Illuminate\Support\Facades\Crypt;
/**
 * Description of Helpers
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class Helpers {

    static function getMainDirectory()
    {
        return config(Constants::MAIN_FOLDER);
    }

    static function getLoggerDirectory()
    {
        $directory = static::getMainDirectory() . '/' . config(Constants::CURRENT_LOGGER_FOLDER);
        return $directory;
    }

    static function getCurrentSyncingDirectory()
    {
        $directory = static::getMainDirectory() . '/' . config(Constants::CURRENT_SYNCING_FOLDER);
        return $directory;
    }

    static function getSyncedDirectory()
    {
        $directory = static::getMainDirectory() . '/' . config(Constants::SYNCED_FOLDER);
        return $directory;
    }

    private static function convertIndexToName($index)
    {
        return sprintf("%08d", $index);
    }

    static function getNewLogFileIndex()
    {
        $fileIndex = static::getCurrentLogState(Constants::CURRENT_FILE_INDEX) + 1;
        static::setCurrentLogState(Constants::CURRENT_FILE_INDEX, $fileIndex);
        return static::convertIndexToName($fileIndex);
    }

    static function getNewLogName()
    {
        $fileName = config(Constants::FILE_PREFIX) . '_' . config(Constants::SERVER_NAME) . '_' . config(Constants::SERVER_ID) . '_' . static::getNewLogFileIndex() . '.log';
        return $fileName;
    }

    static function getCurrentLogFileIndex()
    {
        $fileIndex = static::getCurrentLogState(Constants::CURRENT_FILE_INDEX);
        return static::convertIndexToName($fileIndex);
    }

    static function getCurrentLogName()
    {
        $fileName = config(Constants::FILE_PREFIX) . '_' . config(Constants::SERVER_NAME) . '_' . config(Constants::SERVER_ID) . '_' . static::getCurrentLogFileIndex() . '.log';
        return $fileName;
    }

    static function getNewLogFilePath()
    {
        $path = Helpers::getLoggerDirectory() . '/' . Helpers::getNewLogName();
        return $path;
    }

    static function getCurrentLogFilePath()
    {
        $path = Helpers::getLoggerDirectory() . '/' . Helpers::getCurrentLogName();
        return $path;
    }

    static function getCurrentLogStateFile()
    {
        $path = static::getMainDirectory() . '/' . config(Constants::FILE_CURRENT_STATE) . '.json';
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

    static function setEnvironmentValue($data = array())
    {
        $envPath = base_path('.env');
        if (count($data) > 0) {

            // Read .env-file
            $env = file_get_contents($envPath);

            // Split string on every " " and write into array
            $env = preg_split('/\s/', $env);
            
            // Loop through given data
            foreach ((array) $data as $key => $value) {

                // Loop through .env-data
                foreach ($env as $env_key => $env_value) {

                    // Turn the value into an array and stop after the first split
                    // So it's not possible to split e.g. the App-Key by accident
                    $entry = explode("=", $env_value, 2);

                    // Check, if new key fits the actual .env-key
                    if ($entry[0] == $key) {
                        // If yes, overwrite it with the new one
                        $env[$env_key] = $key . "=" . $value;
                    } else {
                        // If not, keep the old one
                        $env[$env_key] = $env_value;
                    }
                }
            }

            // Turn the array back to an String
            $env = implode("\n", $env);

            // And overwrite the .env with the new data
            file_put_contents($envPath, $env);

            return true;
        } else {
            return false;
        }
    }

    static function decryptLogFile()
    {
        $path      = static::getCurrentLogFilePath();
        $content   = File::get($path);
        $decrypted = Crypt::decryptString($content);
        File::put($path, $decrypted);
    }

    static function encryptLogFile()
    {
        $path      = static::getCurrentLogFilePath();
        $content   = File::get($path);
        $encrypted = Crypt::encryptString($content);
        File::put($path, $encrypted);
    }
}
