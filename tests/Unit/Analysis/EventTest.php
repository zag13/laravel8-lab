<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/27
 * Time: 3:02 下午
 */


namespace Tests\Unit\Analysis;


use App\Utils\Analysis\Events\Dispatcher;
use App\Utils\Analysis\Events\Example;
use PHPUnit\Framework\TestCase;

class EventTest extends TestCase
{
    public function testEvent()
    {
        $this->assertEquals('App\Utils\Analysis\Events\ExampleListener::doSomething(work)',
            (new Dispatcher())->dispatch(new Example('work'))[0]);
    }
}
