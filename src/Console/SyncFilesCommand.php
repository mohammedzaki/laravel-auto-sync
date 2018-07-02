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

namespace AutoSync\Console;

use Illuminate\Console\Command;
use AutoSync\Filesystem\FolderCreator;
use AutoSync\Utils\Helpers;
use DB;
use File;

/**
 * Description of AutoSyncingCommand
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class SyncFilesCommand extends Command
{

    const FILE_NAME           = 'file-name';
    const WITHOUT_DECRYPT     = 'without-decrypt';
    const INSERT_LINE_BY_LINE = 'line-by-line';

    /**
     * SetupFolders .
     *
     * @var FolderCreator
     */
    private $folderCreator;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'autosync:sync-files '
            . '{--' . SyncFilesCommand::FILE_NAME . '= : The NAME of the file or all}'
            . '{--' . SyncFilesCommand::WITHOUT_DECRYPT . '}'
            . '{--' . SyncFilesCommand::INSERT_LINE_BY_LINE . ' }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'start auto sync process on all files or specified file';

    /**
     * Server Name.
     *
     * @var string
     */
    protected $fileName;

    /**
     * Server Name.
     *
     * @var string
     */
    protected $withoutDecrypt;
    protected $insertLineByLine;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(FolderCreator $folderCreator)
    {
        parent::__construct();
        $this->folderCreator = $folderCreator;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->fileName         = $this->option(SyncFilesCommand::FILE_NAME);
        $this->withoutDecrypt   = $this->option(SyncFilesCommand::WITHOUT_DECRYPT);
        $this->insertLineByLine = $this->option(SyncFilesCommand::INSERT_LINE_BY_LINE);
        if (empty($this->fileName)) {
            $this->startSyncingAllLogs();
        } elseif ($this->fileName == 'all') {
            $this->startSyncingAllLogs();
        } else {
            $this->startSyncingLogFile($this->fileName);
        }
    }

    private function startSyncingLogFile($fileName)
    {
        $logfile = Helpers::getCurrentSyncingDirectory() . "/{$fileName}";
        $this->insertLogFileToServer($logfile);
    }

    private function startSyncingAllLogs()
    {
        $files = File::allFiles(Helpers::getCurrentSyncingDirectory());
        sort($files);
        foreach ($files as $logfile) {
            $this->insertLogFileToServer($logfile);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function insertLogFileToServer($logfile)
    {
        if (!$this->withoutDecrypt) {
            Helpers::decryptLogFile($logfile);
        }
        if ($this->insertLineByLine) {
            $this->insertLogFileToServerLineByLine($logfile);
        } else {
            $this->insertWholeLogFileToServer($logfile);
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function insertWholeLogFileToServer($logfile)
    {
        $filename = basename($logfile);
        $sqlLogs  = File::get($logfile);
        logger("ProcessSyncLogFile staring insert to database from file: '{$filename}'");
        DB::beginTransaction();
        try {
            DB::statement($sqlLogs);
            DB::commit();
            logger("ProcessSyncLogFile insert to database success from file: '{$filename}'");
            Helpers::moveFileToSynced($logfile);
        } catch (\Exception $exc) {
            DB::rollBack();
            logger($exc->getMessage());
        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function insertLogFileToServerLineByLine($logfile)
    {
        $filename = basename($logfile);
        //$sqlLogs  = File::get($logfile);
        logger("ProcessSyncLogFile staring insert to database from file: '{$filename}'");
        foreach(file($logfile) as $sql) {
            try {
                DB::statement($sql);
                //logger("Sql: {$sql}");
            } catch (\Exception $exc) {
                
            }
        }
        logger("ProcessSyncLogFile insert to database success from file: '{$filename}'");
        Helpers::moveFileToSynced($logfile);
    }

}
