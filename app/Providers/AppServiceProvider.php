<?php

namespace App\Providers;

use App\Services\Utils\ZLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        /*DB::listen(function ($query) {
            $sql = str_replace('?', "'%s'", $query->sql);
            $sqlInfo = 'execution time: ' . $query->time . 'ms; ' . vsprintf($sql, $query->bindings);
            ZLog::channel('sql')->info($sqlInfo);
        });*/
    }
}
