<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/21
 * Time: 1:58 下午
 */


namespace App\Http\Controllers\Analysis;


use App\Http\Controllers\Core\Controller;
use App\Services\Analysis\Container\A;
use App\Services\Analysis\Container\B;
use App\Services\Analysis\Container\C;
use App\Utils\Analysis\Container;
//use Illuminate\Container\Container;

class ContainerController extends Controller
{
    public function test()
    {
        /**
         * 控制反转（英语：Inversion of Control，缩写为IoC），
         * 是面向对象编程中的一种设计原则，可以用来减低计算机代码之间的耦合度。
         * 其中最常见的方式叫做依赖注入（Dependency Injection，简称DI），
         * 还有一种方式叫“依赖查找”（Dependency Lookup）。
         * https://zh.wikipedia.org/wiki/%E6%8E%A7%E5%88%B6%E5%8F%8D%E8%BD%AC
         */

        /**
         * DI
         * Dependency Injection  依赖注入
         */
//        $c = new C(new B(new A()));
//        $c->doSomething();

        /**
         * 容器初级使用
         * 手动注入所需要的依赖
         */
//        $container = new Container();
//        $container->a = function () {
//            return new A();
//        };
//        $container->b = function ($container) {
//            return new B($container->a);
//        };
//        $container->c = function ($container) {
//            return new C($container->b);
//        };
//        $c = $container->c;
//        $c->doSomething();

        /**
         * 容器高级使用
         *
         */
//        $container = new Container();
//        $container->b = 'App\Services\Analysis\Container\B';
//        $container->c = function ($container) {
//            return new C($container->b);
//        };
//        $c = $container->c;
//
//        $c->doSomething();

        $di = new Container();
        $di->c = 'App\Services\Analysis\Container\C';
        $c = $di->c;

        print_r($c);
        $c->doSomething();
    }
}
