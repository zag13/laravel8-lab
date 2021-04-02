<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/4/1
 * Time: 6:15 下午
 */


namespace App\Models\Mongos;


use App\Models\Core\MongoModel;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class PostsMongo extends MongoModel
{
    use SoftDeletes;

    protected $collection = 'posts';

    protected $dates = ['deleted_at'];
}
