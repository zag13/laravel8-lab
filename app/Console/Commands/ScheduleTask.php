<?php

namespace App\Console\Commands;

use Illuminate\Cache\RedisLock;
use Illuminate\Console\Command;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Storage;

class ScheduleTask extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:task {process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mock Schedule Tasks';

    protected $lock;

    /**
     * Create a new command instance.
     *
     * @param Connection $redis
     */
    public function __construct(Connection $redis)
    {
        parent::__construct();
        $this->lock = new RedisLock($redis, 'schedule_task', 60);
    }

    /**
     * Execute the console command.
     *
     * @return int
     * @throws \Illuminate\Contracts\Cache\LockTimeoutException
     */
    public function handle()
    {
        $this->lock->block(5, function () {
            $processNo = $this->argument('process');
            for ($i = 1; $i <= 10; $i++) {
                $log = "Running Job #{$i} In Process #{$processNo}";
                Storage::disk('local')->append('schedule_task_logs', $log);
            }
        });
    }
}
