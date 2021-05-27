<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/27
 * Time: 4:58 下午
 */


namespace Tests\Feature;


use App\Events\UserLoginEvent;
use Tests\TestCase;

class AnalysisTest extends TestCase
{
    /**
     * 推荐阅读
     * https://github.com/LeoYang90/laravel-source-analysis
     */

    /**
     * 事件分析文章
     * https://www.cnblogs.com/alwayslinger/p/13756847.html
     */
    public function testEvent()
    {
        $this->assertEquals(1, event(new UserLoginEvent(1))[0]);

        $this->assertEquals(['a', 'b', 'c'],
            event(new UserLoginEvent(['a', 'b', 'c']))[0]);
    }
}
