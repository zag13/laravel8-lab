<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/3/31
 * Time: 6:02 下午
 */


namespace App\Http\Controllers;


use App\Http\Controllers\Core\Controller;
use App\Models\Mongo\MongoTestMongo;

class MongoController extends Controller
{
    public function find()
    {
        $data = MongoTestMongo::all()->toArray();

        $data2 = MongoTestMongo::where('site', '=', 'github.com')->get()->toArray();

        dd($data, $data2);
    }
}
