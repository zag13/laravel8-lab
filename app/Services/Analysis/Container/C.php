<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/21
 * Time: 2:04 下午
 */


namespace App\Services\Analysis\Container;


class C
{
    public $b;

    public function __construct(B $b)
    {
        $this->b = $b;
    }

    public function doSomething()
    {
        $this->b->doSomething();

        echo __METHOD__ . PHP_EOL;
    }
}
