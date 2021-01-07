<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/1/6
 * Time: 10:03 下午
 */


namespace App\Logging;


use Monolog\Logger;

class CreateCustomLogger
{
    /**
     * Create a custom Monolog instance.
     *
     * @param array $config
     * @return Logger
     */
    public function __invoke(array $config): Logger
    {
        return new Logger();
    }
}
