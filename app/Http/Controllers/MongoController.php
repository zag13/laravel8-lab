<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/3/31
 * Time: 6:02 下午
 */


namespace App\Http\Controllers;


use App\Http\Controllers\Core\Controller;
use App\Models\Mongo\PostsMongo;
use Illuminate\Support\Facades\DB;

class MongoController extends Controller
{
    protected $postsMongoRepo;

    public function find()
    {
        //$data = TestMMongo::all()->toArray();

        //$data = TestMMongo::where('site', '=', 'github.com')->delete();

        //$data = MongoModel::collection('posts')->get()->toArray();

        //$data = DB::connection('mongodb')->collection('posts')->get()->toArray();

        $data = PostsMongo::find('606472999aa48f142404c0b6', ['*']);

        dd($data);
    }
}
