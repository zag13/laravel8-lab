<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/23
 * Time: 5:44 下午
 */


namespace Tests\Feature\Analysis\Container;


use App\Jobs\ExportJob;
use Illuminate\Container\Container as LaraContainer;
use Illuminate\Contracts\Queue\ShouldQueue;
use stdClass;
use Tests\TestCase;

class BindTest extends TestCase
{
    public function testBind()
    {
        $container = new LaraContainer();

        // 绑定自身 ???
        /*$container->bind('Tests\Feature\Analysis\Container\BindTest', null);
        $thisZ = $container->make('Tests\Feature\Analysis\Container\BindTest');

        $this->assertSame($this, $thisZ);*/

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

        $container->singleton('c', null);

        $this->assertEquals(['name' => 'PHP'], $container->makeWith('c', ['name' => 'PHP']));
        $this->assertEquals(['name' => 'Golang'], $container->makeWith('c', ['name' => 'Golang']));

    }
}
