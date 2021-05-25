<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/23
 * Time: 5:44 下午
 */


namespace Tests\Feature\Analysis;


use App\Jobs\ExportJob;
use App\Services\Analysis\Container\A;
use App\Services\Analysis\Container\B;
use App\Services\Analysis\Container\C;
use App\Services\Analysis\Container\Electricity;
use App\Services\Analysis\Container\Petrol;
use App\Services\Analysis\Container\Tesla;
use Illuminate\Container\Container as LaraContainer;
use Illuminate\Contracts\Queue\ShouldQueue;
use stdClass;
use Tests\TestCase;

class ContainerTest extends TestCase
{
    public function testBind()
    {
        $container = new LaraContainer();

        $container->bind('App\Services\Analysis\Container\A');

        $this->assertEquals('PHP',
            $container->makeWith('App\Services\Analysis\Container\A', ['lang' => 'PHP'])->lang);

        // 绑定闭包
        $class = new stdClass();
        $container->bind('class', function () use ($class) {
            return $class;
        });
        $classZ = $container->make('class');

        $this->assertSame($class, $classZ);

        // 绑定接口
        $container->bind('App\Jobs\ExportJob', 'Illuminate\Contracts\Queue\ShouldQueue');

        $this->assertInstanceOf(ShouldQueue::class,
            $container->build(ExportJob::class));
    }

    public function testBindIf()
    {
        $container = new LaraContainer();
        $container->bind('name', function () {
            return 'PHP';
        });

        $container->bindIf('name', function () {
            return 'Golang';
        });

        $this->assertEquals('PHP', $container->make('name'));
    }

    public function testSingleton()
    {
        $container = new LaraContainer();

        $container->singleton('App\Services\Analysis\Container\A');

        $a = $container->makeWith('App\Services\Analysis\Container\A', ['lang' => 'PHP']);

        $this->assertEquals('PHP',
            $container->makeWith('App\Services\Analysis\Container\A', ['lang' => 'PHP'])->lang);
        $this->assertEquals('Golang',
            $container->makeWith('App\Services\Analysis\Container\A', ['lang' => 'Golang'])->lang);
    }

    public function testInstance()
    {
        $container = new LaraContainer();

        $b = new B(new A());

        $container->instance('B', $b);

        $this->assertEquals($b, $container->make('B'));
    }

    public function testContext()
    {
        $container = new LaraContainer();

        $container->when('App\Services\Analysis\Container\Petrol')
            ->needs('App\Services\Analysis\Container\Fuel')
            ->give(function ($container) {
                $container->php = '是世界上最好的语言';
            });

        $container->when('App\Services\Analysis\Container\Electricity')
            ->needs('App\Services\Analysis\Container\Fuel')
            ->give(function ($container) {
                $container->golang = '哈哈哈哈';
            });

        $this->assertEquals('?', '?');
    }

    public function testArrayAccess()
    {
        $container = new LaraContainer();

        $container[ExportJob::class] = ShouldQueue::class;

        $this->assertTrue(isset($container[ExportJob::class]));

        $this->assertEquals(ShouldQueue::class, $container[ExportJob::class]);

        $this->assertFalse(isset($container['something']));
        unset($container['something']);
        $this->assertFalse(isset($container['something']));
    }

    public function testTag()
    {
        $container = new LaraContainer();

        $container->tag('App\Services\Analysis\Container\C', ['C', 'B']);
        $container->tag('App\Services\Analysis\Container\B', ['B']);

        $this->assertCount(2, $container->tagged('B'));
        $this->assertCount(1, $container->tagged('C'));

//        $this->assertInstanceOf('App\Services\Analysis\Container\C', 'TODO');
//        $this->assertInstanceOf('App\Services\Analysis\Container\B', 'TODO');
    }

    public function testExtend()
    {
        $container = new LaraContainer();

        $container->bind('lang', function () {
            $obj = new stdClass();
            $obj->php = '世界上最好的语言!';
            $obj->c = '呵呵';
            return $obj;
        });

        $obj = new stdClass();
        $obj->php = '虾扯蛋!';

        $container->instance('lang', $obj);  // 之前的 lang 被覆盖了

        $container->extend('lang', function ($obj) {
            $obj->golang = '小弟后来的，不敢说话';
            return $obj;
        });

        $container->extend('lang', function ($obj) {
            $obj->cpp = '哈哈';
            return $obj;
        });

        $this->assertEquals('虾扯蛋!', $container['lang']->php);
        $this->assertEquals('小弟后来的，不敢说话', $container['lang']->golang);
        $this->assertEquals('哈哈', $container['lang']->cpp);
    }

    public function testRebinding()
    {
        $container = new LaraContainer();

        $container->bind('fuel', function () {
            return new Electricity();
        });

        // FIXME shared 有什么用？
        // 去掉后，即使 car 已实例化其成员变量也会发生变化
        // 不去吧，又只有重新绑定
        $container->bind('car', function ($container) {
            return new Tesla($container['fuel']);
        }, true);

        $this->assertEquals(1.58 * 3, $container['car']->reFuel(3));

        $container->bind('fuel', function () {
            return new Petrol();
        });

        $this->assertEquals(1.58 * 3, $container['car']->reFuel(3));

        $container->bind('car', function ($container) {
            return new Tesla($container->rebinding('fuel', function ($container, $fuel) {
                $container['car']->setFuel($fuel);
            }));
        });

        $this->assertEquals(6.67 * 3, $container['car']->reFuel(3));
    }

    public function testMake()
    {
        $container = new LaraContainer();

        $c = $container->make('App\Services\Analysis\Container\C');
        $this->assertInstanceOf('App\Services\Analysis\Container\C', $c);
        $this->assertInstanceOf('App\Services\Analysis\Container\B', $c->b);
        $this->assertInstanceOf('App\Services\Analysis\Container\A', $c->b->a);

        $a = $container->make('App\Services\Analysis\Container\a');
        $this->assertEquals('php', $a->lang);
    }

    public function testCallClosure()
    {
        $container = new LaraContainer();

        $res = $container->call(function (stdClass $a, $b = []) {
            return func_get_args();
        }, ['b' => 'string']);

        $this->assertInstanceOf('stdClass', $res[0]);
        $this->assertEquals('string', $res[1]);
    }

    public function testCallStatic()
    {
        $container = new LaraContainer();

        $this->assertEquals('php',
            $container->call(B::class . '@getBestLangS'));
        $this->assertEquals('php',
            $container->call(B::class . '::getBestLangS'));
        $this->assertEquals('golang',
            $container->call([B::class, 'getBestLangS'], ['a' => new A('golang')]));
    }

    public function testCallNotStatic()
    {
        $container = new LaraContainer();

        $this->assertEquals('php',
            $container->call([$container->make(B::class), 'getBestLang']));
        $this->assertEquals('php',
            $container->call(B::class . '@getBestLang'));
        $this->assertEquals('golang',
            $container->call(B::class . '@getBestLang', ['a' => new A('golang')]));
    }

    public function testBindMethod()
    {
        $container = new LaraContainer();

        $container->bindMethod(A::class . '@test', function () {
            return 'test';
        });

        $this->assertEquals('test', $container->call([A::class, 'test']));
    }

    public function testAlias()
    {
        $container = new LaraContainer();

        $container->alias('server', 'a');
        $container->alias('a', 'b');
        $container->alias('b', 'c');

        $this->assertEquals('server', $container->getAlias('c'));
    }

    public function testEvent()
    {
        $container = new LaraContainer();

        $container->alias('stdClass', 'std');

        $container->resolving('std', function ($obj) {
            $obj->lang = 'PHP';
        });

        $container->bind('stdClass', function () {
            return new stdClass();
        });

        $this->assertEquals('PHP', $container->make('stdClass')->lang);
    }

    public function testWrap()
    {
        $container = new LaraContainer();

        $res = $container->wrap(function (stdClass $a, $b = []) {
            return func_get_args();
        }, ['b' => 'string']);

        $this->assertInstanceOf('Closure', $res);

        $res2 = $container->call(function (stdClass $a, $b = []) {
            return func_get_args();
        }, ['b' => 'string']);

        $this->assertEquals($res2[0], $res()[0]);
        $this->assertEquals($res2[1], $res()[1]);
    }

    public function testFactory()
    {
        $container = new LaraContainer();

        $container->bind('lang', function () {
            return 'PHP';
        });

        $factory = $container->factory('lang');

        $this->assertEquals($container->make('lang'), $factory());
    }

    public function testFlush()
    {
        $container = new LaraContainer();

        $container->bind('lang', function () {
            return 'PHP';
        }, true);

        $container->alias('language', 'lang');

        $this->assertTrue($container->isShared('lang'));
        $this->assertArrayHasKey('lang', $container->getBindings());
        $this->assertEquals('language', $container->getAlias('lang'));

        $container->flush();

        $this->assertFalse($container->resolved('lang'));
        $this->assertEmpty($container->getBindings());
        $this->assertFalse($container->isShared('lang'));
        $this->assertFalse($container->isAlias('language'));
    }

    public function testReboundListeners()
    {
        unset($_SERVER['__test.rebind']);

        $container = new LaraContainer();

        $container->rebinding('lang', function () {
            $_SERVER['__test.rebind'] = true;
        });

        $container->bind('lang', function () {

        });

        $this->assertFalse(isset($_SERVER['__test.rebind']));

        $container->make('lang');

        $container->bind('lang', function () {

        });

        $this->assertTrue($_SERVER['__test.rebind']);
    }
}
