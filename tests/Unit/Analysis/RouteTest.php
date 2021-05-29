<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/28
 * Time: 1:46 下午
 */


namespace Tests\Unit\Analysis;


use Closure;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
use PHPUnit\Framework\TestCase;


class RouteTest extends TestCase
{
    /**
     * Pipeline
     * 原始数据 ---> 【前置管道】 ---> 目标处理逻辑 ---> 【后置管道】 ---> 结果数据
     */
    public function testPipeline()
    {
        $pipe1 = function ($num, Closure $next) {
            return $next($num + 1);
        };

        $pipe2 = function ($num, Closure $next) {
            if ($num > 7) return $num;
            return $next($num + 3);
        };

        $pipe3 = function ($num, Closure $next) {
            return $next($num) * 2;
        };

        $pipe4 = function ($num, Closure $next) {
            return $next($num + 6);
        };

        $pipes = [$pipe1, $pipe2, $pipe3, $pipe4];

        $this->assertEquals(30, (new Pipeline)
            ->send(5)
            ->through($pipes)
            ->then(function ($num) {
                return $num;
            }));

        $this->assertEquals(8, (new Pipeline)
            ->send(7)
            ->through($pipes)
            ->then(function ($num) {
                return $num;
            }));
    }

    /**
     * 控制器方法的参数绑定
     * src/Illuminate/Foundation/Http/Kernel.php ==> handle()
     * ==> sendRequestThroughRouter() ==> Pipeline->then($this->dispatchToRouter())
     * ==> $this->router->dispatch($request) ==> $this->runRouteWithinStack($route, $request)
     * ==> $route->run() ==> $this->runController()
     * ==> src/Illuminate/Routing/ControllerDispatcher.php ==> dispatch()
     * ==> resolveClassMethodDependencies()
     * ==> resolveMethodDependencies()
     *
     */



    public function testRedirect()
    {
        $url = new UrlGenerator(
            new RouteCollection(),
            Request::create('https://github.com')
        );

        $this->assertEquals('https://github.com/zs368', $url->to('zs368'));
    }
}
