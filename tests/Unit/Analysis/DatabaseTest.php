<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/31
 * Time: 10:26 上午
 */


namespace Tests\Unit\Analysis;


use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    // 注册服务、启动连接等自己追下源码就行了
    // laravel 与 PDO 的交互，看下 src/Illuminate/Database/Connection.php
    // DB 的一些方法，看下 src/Illuminate/Database/Schema/...
    // Eloquent 在 DB 上实现了一些有趣的特性，例：修改器（setAttribute）、访问器
    // 简单的理解 DB 更贴近数据库，权限更高。 Eloquent 的粒度更细，也有了更多实用的方法。

}
