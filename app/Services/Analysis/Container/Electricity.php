<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/24
 * Time: 5:15 下午
 */


namespace App\Services\Analysis\Container;


class Electricity implements Fuel
{
    public function getPrice()
    {
        return 1.58;
    }
}
