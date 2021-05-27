<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/27
 * Time: 4:07 下午
 */


namespace App\Utils\Analysis\Events;


class Example
{
    public $something;

    public function __construct($something = 'nothing')
    {
        $this->something = $something;
    }
}
