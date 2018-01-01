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

namespace AutoSync;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use AutoSync\Format\SqlLineFormat;
use AutoSync\Sql\PrepareSql;
use AutoSync\Sql\SqlChecker;
use AutoSync\Filesystem\LogFileHandler;
use AutoSync\Filesystem\Constants;
use AutoSync\Filesystem\Helpers;

/**
 * Description of SqlLogger
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class SqlLogger {

    /**
     * @var LogFileHandler 
     */
    private $logFileHandler;

    /**
     *
     * @var PrepareSql 
     */
    private $prepareSql;

    /**
     *
     * @var SqlChecker 
     */
    private $sqlChecker;

    public function __construct()
    {
        $this->logFileHandler = new LogFileHandler();
        $this->prepareSql     = new PrepareSql();
        $this->sqlChecker     = new SqlChecker;
    }

    public function log($sql, $bindings)
    {
        if ($this->sqlChecker->isDML($sql) && $this->sqlChecker->checkIgnoredTable($sql)) {
            $sqlQuery = $this->prepareSql->prepare($sql, $bindings);

            // Create a handler
            $stream = new StreamHandler($this->logFileHandler->getCurrentLogFile(), Logger::INFO);
            $stream->setFormatter(new SqlLineFormat());

            // create a log channel
            $log = new Logger(Constants::CHANNEL);
            $log->pushHandler($stream);

            // add records to the log
            $log->info($sqlQuery, ['index' => $this->logFileHandler->getNewRecord()]);
            Helpers::encryptLogFile();
        }
    }

    

}
