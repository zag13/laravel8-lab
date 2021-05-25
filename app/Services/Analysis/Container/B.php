<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/21
 * Time: 2:01 下午
 */


namespace App\Services\Analysis\Container;


class B
{
    public $a;

    public function __construct(A $a)
    {
        $this->a = $a;
    }

    public function doSomething()
    {
        $this->a->doSomething();

        echo __METHOD__ . PHP_EOL;
    }
}
