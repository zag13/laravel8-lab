<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/26
 * Time: 4:14 下午
 */


namespace Tests\Unit\Analysis;


use App\Utils\Analysis\Facades\Example;
use PHPUnit\Framework\TestCase;

class FacadeTest extends TestCase
{
    public function testFacade()
    {
        $this->assertEquals('hello', Example::sayWhat('hello'));
    }
}
