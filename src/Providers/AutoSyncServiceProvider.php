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

namespace AutoSync\Providers;

use Illuminate\Support\ServiceProvider;
use DB;
use AutoSync\SqlLogger;
use AutoSync\Console\AutoSyncConfigCommand;
use AutoSync\Console\AutoSyncingCommand;
use AutoSync\Console\AutoSyncResetCommand;
use AutoSync\Console\SyncFilesCommand;
use Illuminate\Console\Scheduling\Schedule;
use AutoSync\Utils\Constants;

/**
 * Description of AutoSyncServiceProvider
 *
 * @author Mohammed Zaki mohammedzaki.dev@gmail.com
 */
class AutoSyncServiceProvider extends ServiceProvider {

    /**
     *
     * @var SqlLogger 
     */
    private $sqlLogger;

    public function __construct($app)
    {
        parent::__construct($app);
        $this->sqlLogger = new SqlLogger();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfig();
        $this->setQueryListener();
        $this->registerCommands();
        $this->registerSchedulingCommands();
        $this->registerRoutes();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfig();
    }

    /**
     * Set Query Listener.
     *
     * @return void
     */
    private function setQueryListener()
    {
        if (!config(Constants::SERVER_IS_MASTER)) {
            DB::listen(function ($query) {
                // $query->sql
                // $query->bindings
                // $query->time
                $this->sqlLogger->log($query->sql, $query->bindings);
            });
        }
    }

    /**
     * Publish config files.
     *
     * @return void
     */
    private function publishConfig()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/../config/autosync.php' => config_path('autosync.php'),
        ]);
    }

    /**
     * Merges user's and autosync's configs.
     *
     * @return void
     */
    private function mergeConfig()
    {
        $this->mergeConfigFrom(
                __DIR__ . '/../config/autosync.php', 'autosync'
        );
    }

    private function registerRoutes()
    {
        $routeConfig = [
            'namespace'  => 'AutoSync\Controllers',
            'prefix'     => '',
            'middleware' => 'api',
        ];

        $this->getRouter()->group($routeConfig, function($router) {
            $router->post(config(Constants::MASTER_SERVER_SYNC_API), [
                'uses' => 'AutoSyncController@pushNewSyncFile',
                'as'   => 'autosync.pushNewSyncFile',
            ]);
        });
    }

    /**
     * Get the active router.
     *
     * @return Router
     */
    private function getRouter()
    {
        return $this->app['router'];
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AutoSyncConfigCommand::class,
                AutoSyncingCommand::class,
                AutoSyncResetCommand::class,
                SyncFilesCommand::class
            ]);
        }
    }

    private function registerSchedulingCommands()
    {
        $this->app->booted(function () {
            $schedule = $this->app->make(Schedule::class);
            if ($schedule instanceof Schedule) {
                if (!config(Constants::SERVER_IS_MASTER)) {
                    $schedule->command('autosync:start-sync')->cron(config(Constants::SYNC_SCHEDULE_TIME))->withoutOverlapping();
                }
                if (config(Constants::SERVER_IS_MASTER)) {
                    $schedule->command('queue:work ' . config(Constants::SYNC_QUEUE_DRIVER) . ' --queue=' . config(Constants::SYNC_QUEUE_NAME))->everyMinute()->withoutOverlapping();
                }
            }
        });
    }

}
