<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/27
 * Time: 3:44 下午
 */


namespace App\Utils\Analysis\Events;


class ExampleListener
{
    public function doSomething(Example $example)
    {
        return __METHOD__ . '(' . $example->something . ')';
    }
}
