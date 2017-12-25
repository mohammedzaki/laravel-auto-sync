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

/**
 * Description of SetupFolders
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class SetupFolders {

    private function createDirectory($directory)
    {
        if (!File::exists($directory)) {
            File::makeDirectory($directory, 0777, true, true);
            $this->createGitIgnore($directory);
        }
        return $directory;
    }

    private function createGitIgnore($directory)
    {
        $fullPath = $directory . '/.gitignore';
        File::put($fullPath, '* \n !.gitignore');
    }

    private function createMainDirectory()
    {
        return $this->createDirectory(Helpers::getMainDirectory());
    }

    private function createLoggerDirectory()
    {
        return $this->createDirectory(Helpers::getLoggerDirectory());
    }

    private function createCurrentSyncingDirectory()
    {
        return $this->createDirectory(Helpers::getCurrentSyncingDirectory());
    }

    private function createSyncedDirectory()
    {
        return $this->createDirectory(Helpers::getSyncedDirectory());
    }

    private function createLogCurrentStateFile()
    {
        $path                    = Helpers::getCurrentLogStateFile();
        $data['current_index']   = '00000001';
        $data['current_syncing'] = '';
        $data['current_record']  = 0;
        if (!File::exists($path)) {
            File::put($path, json_encode(collect($data)));
        }
    }

    public function createFolders()
    {
        $this->createMainDirectory();
        $this->createLoggerDirectory();
        $this->createCurrentSyncingDirectory();
        $this->createSyncedDirectory();
        $this->createLogCurrentStateFile();
    }
}
