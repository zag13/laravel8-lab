<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/24
 * Time: 5:51 下午
 */


namespace App\Services\Analysis\Container;


class Petrol implements Fuel
{
    public function getPrice()
    {
        return 6.67;
    }
}
