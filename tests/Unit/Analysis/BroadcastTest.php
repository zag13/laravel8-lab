<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/6/17
 * Time: 2:50 下午
 */


namespace Tests\Unit\Analysis;


use PHPUnit\Framework\TestCase;

class BroadcastTest extends TestCase
{
    // 用 Xdebug 慢慢追源码
    // 实战部分看消息推送的代码就行
    // 主要就是
    //     1、后端推送redis
    //     2、laravel-echo-server订阅redis
    //     3、laravel-echo订阅laravel-echo-server
}
