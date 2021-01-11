<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/1/6
 * Time: 4:34 下午
 */


namespace App\Services\Utils;


use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use function Symfony\Component\Translation\t;

class ZLog
{
    public static function channel($name)
    {
        $logger = new Logger($name);
        $logger->pushHandler(self::handles($name));
        return $logger;
    }

    private static function handles($directory)
    {
        $date = date('Y-m-d', time());

        // Create a handler
        $stream = new StreamHandler(storage_path('logs/' . $directory . "/$date.log"));
        return $stream->setFormatter(self::format());
    }

    private static function format()
    {
        // the default date format is "Y-m-d\TH:i:sP"
        $dateFormat = "Y-m-d H:i:s";
        // the default output format is "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n"
        $output = "[%datetime%] %channel%.%level_name%: %message% %context%\n";
        // finally, create a formatter
        return new LineFormatter($output, $dateFormat, true, true);
    }
}
