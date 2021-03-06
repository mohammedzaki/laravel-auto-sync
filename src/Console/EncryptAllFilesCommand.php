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
use File;

/**
 * Description of AutoSyncingCommand
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class EncryptAllFilesCommand extends Command
{

    const FILE_NAME = 'file-name';

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
    protected $signature = 'autosync:encrypt-files '
            . '{--' . EncryptAllFilesCommand::FILE_NAME . '= : The NAME of the file or all}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'encrypt all files or specified file of sync process';

    /**
     * Server Name.
     *
     * @var string
     */
    protected $fileName;

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
        $this->fileName = $this->option(EncryptAllFilesCommand::FILE_NAME);
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
        $this->encryptLogFile($logfile);
    }

    private function startSyncingAllLogs()
    {
        $files = File::allFiles(Helpers::getCurrentSyncingDirectory());
        sort($files);
        foreach ($files as $logfile) {
            Helpers::encryptLogFile($logfile);
        }
    }

}
