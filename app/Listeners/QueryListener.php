<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/1/11
 * Time: 3:04 下午
 */


namespace App\Listeners;


use App\Services\Utils\ZLog;
use Illuminate\Database\Events\QueryExecuted;

class QueryListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     * @return void
     */
    public function handle(QueryExecuted $event)
    {
        $sqlLog = config('setting.logSql');
        if ($sqlLog !== true) return;

        $sql = str_replace('?', "'%s'", $event->sql);

        $sqlInfo = 'execution time: ' . $event->time . 'ms; ' . vsprintf($sql, $event->bindings);

        ZLog::channel('sql')->info($sqlInfo);
    }
}
