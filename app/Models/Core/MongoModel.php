<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/3/31
 * Time: 6:11 下午
 */


namespace App\Models\Core;

use Jenssegers\Mongodb\Eloquent\Model;

class MongoModel extends Model
{
    protected $connection = 'mongodb';
}
