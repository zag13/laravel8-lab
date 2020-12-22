<?php

use App\Http\Controllers\System\AuthController;
use App\Http\Controllers\Test\TestController;
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
    'middleware' => 'jwt.auth',
    'prefix' => 'test'
], function () {
    Route::get('user', [TestController::class, 'user']);

    Route::post('fileReader', [TestController::class, 'fileReader']);
    Route::get('fileExport', [TestController::class, 'fileExport']);
});

