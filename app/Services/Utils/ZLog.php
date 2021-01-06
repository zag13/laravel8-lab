<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/1/6
 * Time: 4:34 下午
 */


namespace App\Services\Utils;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;

class ZLog
{
    public static function info($msg = '', $name = 'info')
    {
        $log = new Logger($name);
//        $log->setHandlers()

        $date = date('Y-m-d', time());
        $log->pushHandler(new StreamHandler(storage_path('logs/info/' . $date . '.log'), Logger::INFO));
        $log->info($msg);
    }

    public static function custom($directory, $name, $msg = '', $level = Logger::INFO)
    {
        if (!empty($directory)) throw new \Exception('未定义目录名');
        if (!empty($name)) throw new \Exception('未定义消息主题');

        $log = new Logger($name);
        $date = date('Y-m-d', time());
        $log->pushHandler(new StreamHandler(storage_path('logs/' . $directory . "/$date.log"), $level));
        $log->info($msg);
    }

    private function handles()
    {
    }
}
