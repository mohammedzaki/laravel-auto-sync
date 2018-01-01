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

/**
 * Description of LogFileHandler
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class LogFileHandler {

    /**
     * SetupFolders .
     *
     * @var SetupFolders
     */
    private $setupFolders;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->setupFolders = new SetupFolders();
    }

    public function getCurrentLogFile()
    {
        if (!$this->checkMaxRecords()) {
            $this->createNewLogFile();
        }
        Helpers::decryptLogFile();
        return Helpers::getCurrentLogFilePath();
    }

    public function createNewLogFile()
    {
        Helpers::setCurrentLogState(Constants::CURRENT_RECORD, 0);
        $this->setupFolders->createNewLogFile();
    }

    public function getMaxRecord()
    {
        return Helpers::getCurrentLogState(Constants::CURRENT_RECORD);
    }

    public function getNewRecord()
    {
        $newRecord = $this->getMaxRecord() + 1;
        Helpers::setCurrentLogState(Constants::CURRENT_RECORD, $newRecord);
        return $newRecord;
    }

    public function checkMaxRecords()
    {
        if ($this->getMaxRecord() < config(Constants::MAX_RECORDS)) {
            return TRUE;
        }
        return FALSE;
    }

}
