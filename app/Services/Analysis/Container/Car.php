<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/24
 * Time: 5:13 下午
 */


namespace App\Services\Analysis\Container;


abstract class Car
{
    protected $fuel;

    public function __construct(Fuel $fuel)
    {
        $this->fuel = $fuel;
    }

    public function refuel($degrees)
    {
        return $degrees * $this->fuel->getPrice();
    }

    public function setFuel(Fuel $fuel)
    {
        $this->fuel = $fuel;
    }

}
