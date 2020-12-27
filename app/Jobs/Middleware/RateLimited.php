<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/25
 * Time: 7:25 下午
 */


namespace App\Jobs\Middleware;


use Illuminate\Support\Facades\Redis;

class RateLimited
{

    /**
     * Process the queued job.
     *
     * @param mixed    $job
     * @param callable $next
     * @return mixed
     * @throws \Illuminate\Contracts\Redis\LimiterTimeoutException
     */
    public function handle($job, $next)
    {
        Redis::throttle('key')
            ->block(0)->allow(1)->every(5)
            ->then(function () use ($job, $next) {
                // Lock obtained

                $next($job);
            }, function () use ($job) {
                // Could not obtain lock ...

                $job->release(5);
            });
    }
}