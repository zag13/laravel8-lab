<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/21
 * Time: 2:00 下午
 */


namespace App\Services\Analysis\Container;


class A
{
    public $lang;

    public function __construct($lang = 'php')
    {
        $this->lang = $lang;
    }

    public function doSomething()
    {
        echo __METHOD__ . PHP_EOL;
    }
}
