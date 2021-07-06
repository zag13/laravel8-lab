<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/6/17
 * Time: 2:49 下午
 */


namespace Tests\Unit\Analysis;


use PHPUnit\Framework\TestCase;

class QueueTest extends TestCase
{
    // 用 Xdebug 慢慢追源码
    // 主要入口：
    //     src/Illuminate/Queue/Console/WorkCommand.php
    //     src/Illuminate/Queue/Console/ListenCommand.php
    // 常驻内存原因：
    //     src/Illuminate/Queue/Worker.php -> daemon()
    //     src/Illuminate/Queue/Listener.php -> listen()

    public function testQueue()
    {
    }
}
