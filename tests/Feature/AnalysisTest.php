<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/27
 * Time: 4:58 下午
 */


namespace Tests\Feature;


use App\Events\UserLoginEvent;
use App\Models\User;
use Tests\TestCase;

class AnalysisTest extends TestCase
{
    public function testEvent()
    {
        $this->assertEquals(1, event(new UserLoginEvent(1))[0]);
        
        $this->assertEquals(['a', 'b', 'c'],
            event(new UserLoginEvent(['a', 'b', 'c']))[0]);
    }
}
