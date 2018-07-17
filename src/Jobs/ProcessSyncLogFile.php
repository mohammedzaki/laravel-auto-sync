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

namespace AutoSync\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use AutoSync\Utils\Helpers;
use AutoSync\Console\SyncFilesCommand;
use Exception;
use Artisan;
use File;
use DB;

class ProcessSyncLogFile implements ShouldQueue
{

    use Dispatchable,
        InteractsWithQueue,
        Queueable,
        SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 3;

    /**
     * @var string Description
     */
    private $logFilePath;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Helpers::decryptLogFile($this->logFilePath);
        $filename = basename($this->logFilePath);
        $sqlLogs  = File::get($this->logFilePath);
        logger("ProcessSyncLogFile staring insert to database from file: '{$filename}'");
        DB::beginTransaction();
        DB::statement($sqlLogs);
        DB::commit();
        logger("ProcessSyncLogFile insert to database success from file: '{$filename}'");
        Helpers::moveFileToSynced($this->logFilePath);
    }

    /**
     * The job failed to process.
     *
     * @param  Exception  $exception
     * @return void
     */
    public function failed(Exception $exception)
    {
        DB::rollBack();
        $filename = basename($this->logFilePath);
        Helpers::encryptLogFile($this->logFilePath);
        logger("auto-sync error at file '{$filename}': {$exc->getMessage()}");
        if ($this->attempts() == $this->tries) {
            logger("auto-sync file '{$filename}' has been forced sync");
            Artisan::call("autosync:sync-files --line-by-line", [
                SyncFilesCommand::FILE_NAME => $this->logFilePath
            ]);
        }
    }

}
