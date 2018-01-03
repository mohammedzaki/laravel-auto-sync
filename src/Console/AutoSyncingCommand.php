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
use AutoSync\Utils\Constants;
use GuzzleHttp\Client;
use File;

/**
 * Description of AutoSyncingCommand
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class AutoSyncingCommand extends Command {

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
    protected $signature = 'autosync:start-sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'start auto sync process';

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
        $this->syncAllLogFiles();
    }

    private function syncAllLogFiles()
    {
        $files = File::allFiles(Helpers::getLoggerDirectory());
        foreach ($files as $logfile) {
            $filename = basename($logfile);
            if ($filename != Helpers::getCurrentLogName()) {
                $this->startSyncFile($logfile);
            }
        }
    }

    private function startSyncFile($logfile)
    {
        if ($this->moveFileToSyncing($logfile)) {
            $this->postLogFileToServer(Helpers::getCurrentLogState(Constants::CURRENT_SYNCING_FILE));
        }
    }

    private function postLogFileToServer($logfile)
    {
        $client   = new Client();
        $filename = basename($logfile);
        logger("auto sync process started on file {$filename}");
        $res      = $client->request('POST', config(Constants::MASTER_SERVER_URL) . config(Constants::MASTER_SERVER_SYNC_API), [
            'multipart' => [
                [
                    'name'     => Constants::API_LOG_FILE,
                    'contents' => File::get($logfile),
                    'filename' => $filename
                ],
                [
                    'name'     => 'username',
                    'contents' => config(Constants::MASTER_SERVER_USERNAME)
                ],
                [
                    'name'     => 'password',
                    'contents' => config(Constants::MASTER_SERVER_PASSWORD)
                ]
            ],
        ]);
        if ($res->getStatusCode() == 200) {
            logger("auto sync success on file {$filename}");
        } else {
            logger("auto sync fail on file {$filename}");
        }
    }

    private function moveFileToSyncing($logfile)
    {
        $filename    = basename($logfile);
        $syncingfile = Helpers::getCurrentSyncingDirectory() . '/' . $filename;
        logger("Moving logfile {$filename}");
        if (!File::move($logfile, $syncingfile)) {
            logger("Couldn't move file {$filename}");
            return FALSE;
        } else {
            Helpers::setCurrentLogState(Constants::CURRENT_SYNCING_FILE, $syncingfile);
            return TRUE;
        }
    }

}
