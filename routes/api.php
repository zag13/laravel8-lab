<?php

use App\Http\Controllers\Analysis\ContainerController;
use App\Http\Controllers\System\AuthController;
use App\Http\Controllers\System\PermissionController;
use App\Http\Controllers\Test\ESController;
use App\Http\Controllers\Test\ExcelController;
use App\Http\Controllers\Test\MongoController;
use App\Http\Controllers\Test\RedisController;
use App\Http\Controllers\Test\SqlController;
use App\Http\Controllers\Test\TestController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});*/


// 'prefix' => 'auth'
Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});


// 'prefix' => 'permission'
Route::group([
    'middleware' => 'jwt.auth',
    'prefix' => 'permission'
], function () {
    Route::get('user', [PermissionController::class, 'user']);
});


// 'prefix' => 'test'
Route::group([
//    'middleware' => 'jwt.auth',
    'prefix' => 'test'
], function () {
    Route::get('user', [TestController::class, 'user']);
    Route::get('collect', [TestController::class, 'collect']);
    Route::get('broadcast', [TestController::class, 'broadcast']);
    Route::get('test', [TestController::class, 'test']);
});


// 'prefix' => 'analysis'
Route::group([
    'prefix' => 'analysis'
], function () {
    Route::group([
        'prefix' => 'container'
    ], function () {
        Route::get('test', [ContainerController::class, 'test']);
        Route::get('diy', [ContainerController::class, 'DIY']);
        Route::get('laraContainer', [ContainerController::class, 'laraContainer']);
    });
});


// 'prefix' => 'excel'
Route::group([
    'prefix' => 'excel'
], function () {
    Route::get('readExcel', [ExcelController::class, 'readExcel']);
    Route::get('export2Browser', [ExcelController::class, 'export2Browser']);
    Route::get('export2Local', [ExcelController::class, 'export2Local']);
    Route::get('bigDataExport2', [ExcelController::class, 'bigDataExport2']);
    Route::get('bigDataExport4', [ExcelController::class, 'bigDataExport4']);
    Route::get('download', [ExcelController::class, 'download']);
});


// 'prefix' => 'sql'
Route::group([
    'prefix' => 'sql'
], function () {
    Route::get('database', [SqlController::class, 'database']);
    Route::get('orm', [SqlController::class, 'orm']);
    Route::get('relationships', [SqlController::class, 'relationships']);

    /*
     * TODO 没法依赖注入
     * 1、获取传递的参数
     * 2、通过反射获取 method 中的依赖
     * 3、依赖注入 && 参数绑定
     * 4、实例化
     */
    Route::get('{uri}', function ($uri) {
        $controller = new SqlController();
        if (!method_exists($controller, $uri)) throw new Exception('not found');
        return $controller->$uri();
    });
});


// 'prefix' => 'redis'
Route::group([
    'prefix' => 'redis'
], function () {
    Route::get('string', [RedisController::class, 'string']);
    Route::get('set', [RedisController::class, 'set']);
    Route::get('list', [RedisController::class, 'list']);
    Route::get('sortedSet', [RedisController::class, 'sortedSet']);
    Route::get('hash', [RedisController::class, 'hash']);
    Route::get('hyperLogLog', [RedisController::class, 'hyperLogLog']);
    Route::get('geo', [RedisController::class, 'geo']);
    Route::get('bit', [RedisController::class, 'bit']);
    Route::get('database', [RedisController::class, 'database']);
    Route::get('expire', [RedisController::class, 'expire']);
    Route::get('transaction', [RedisController::class, 'transaction']);
    Route::get('lua', [RedisController::class, 'lua']);
    Route::get('persistence', [RedisController::class, 'persistence']);
    Route::get('pubsub', [RedisController::class, 'pubsub']);
    Route::get('replication', [RedisController::class, 'replication']);
    Route::get('cliAndSer', [RedisController::class, 'cliAndSer']);
    Route::get('config', [RedisController::class, 'config']);
    Route::get('debug', [RedisController::class, 'debug']);
    Route::get('internalCommand', [RedisController::class, 'internalCommand']);
});


// 'prefix' => 'mongo'
Route::group([
    'prefix' => 'mongo'
], function () {
    Route::get('find', [MongoController::class, 'find']);
    // 感觉这种用法不怎么好 1、不满足会继续向下寻找至结束 2、感觉上面的会把下面的路由覆盖
    Route::get('show/{id}', [MongoController::class, 'show'])->where('id', '[A-Za-z0-9]+');
    Route::get('popular', [MongoController::class, 'popular']);
});


// 'prefix' => 'es'
Route::group([
    'prefix' => 'es'
], function () {
    Route::get('elasticsearch', [ESController::class, 'elasticsearch']);
    Route::get('search', [ESController::class, 'search']);
    Route::get('faker', [ESController::class, 'faker']);
});
