<?php

use App\Http\Controllers\System\AuthController;
use App\Http\Controllers\MongoController;
use App\Http\Controllers\RedisController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\System\PermissionController;
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


Route::group([
    'prefix' => 'auth'
], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
});


Route::group([
    'middleware' => 'jwt.auth',
    'prefix' => 'permission'
], function () {
    Route::get('user', [PermissionController::class, 'user']);
});


Route::group([
//    'middleware' => 'jwt.auth',
    'prefix' => 'test'
], function () {
    Route::get('user', [TestController::class, 'user']);
    Route::post('fileReader', [TestController::class, 'fileReader']);
    Route::get('fileExport', [TestController::class, 'fileExport']);
    Route::get('queue', [TestController::class, 'queue']);
    Route::get('download', [TestController::class, 'download']);
    Route::get('collect', [TestController::class, 'collect']);
    Route::get('broadcast', [TestController::class, 'broadcast']);
    Route::get('relationships', [TestController::class, 'relationships']);
    Route::get('database', [TestController::class, 'database']);
    Route::get('orm', [TestController::class, 'orm']);
    Route::get('elasticsearch', [TestController::class, 'elasticsearch']);
    Route::get('faker', [TestController::class, 'faker']);
    Route::get('search', [TestController::class, 'search']);
});

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


Route::group([
    'prefix' => 'mongo'
], function () {
   Route::get('find', [MongoController::class, 'find']);
   // 感觉这种用法不怎么好 1、不满足会继续向下寻找至结束 2、感觉上面的会把下面的路由覆盖
   Route::get('show/{id}', [MongoController::class, 'show'])->where('id', '[A-Za-z0-9]+');
   Route::get('popular', [MongoController::class, 'popular']);
});
