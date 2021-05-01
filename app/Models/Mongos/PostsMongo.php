<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/4/1
 * Time: 6:15 下午
 */


namespace App\Models\Mongos;


use App\Models\Core\Mongo;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class PostsMongo extends Mongo
{
    use SoftDeletes;

    protected $collection = 'posts';

    protected $dates = ['deleted_at'];
}
