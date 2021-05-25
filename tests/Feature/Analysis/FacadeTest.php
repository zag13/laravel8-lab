<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/25
 * Time: 5:56 下午
 */


namespace Tests\Feature\Analysis;


use App\Utils\Analysis\Facades\Example;
use Tests\TestCase;

class FacadeTest extends TestCase
{
    public function testFacade()
    {
        $this->assertEquals('hello', Example::sayWhat('hello'));
    }
}
