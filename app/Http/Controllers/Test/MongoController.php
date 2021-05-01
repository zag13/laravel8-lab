<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/3/31
 * Time: 6:02 下午
 */


namespace App\Http\Controllers\Test;


use App\Http\Controllers\Core\Controller;
use App\Repositories\PostsMongoRepository;

class MongoController extends Controller
{
    protected $postsMongoRepo;

    public function __construct(PostsMongoRepository $postsMongoRepo)
    {
        $this->postsMongoRepo = $postsMongoRepo;
    }

    public function find()
    {
        //$data = PostsMongo::orWhere('rank', 'desc')->get()->toArray();

        //$data = TestMMongo::where('site', '=', 'github.com')->delete();

        //$data = Mongo::collection('posts')->get()->toArray();

        //$data = DB::connection('mongodb')->collection('posts')->get()->toArray();

        //$data = PostsMongo::where('_id', '=', '606474e89aa48f142404c0bb')->increment('views')->save();

        //$data = DownloadLogModel::where('id', '=', 1)->increment('file_siz');

        //dump($data);
    }

    // 浏览文章
    public function show($id)
    {
        $post = $this->postsMongoRepo->getById($id);
        if (is_null($post)) return $this->respFail('没有查询到对应文章');
        $views = $this->postsMongoRepo->addViews($post);
        return "Show Post #{$post->id}, Views: {$views}";
    }

    // 获取热门文章排行榜
    public function popular()
    {
        $posts = $this->postsMongoRepo->trending();

        if ($posts) dump($posts->toArray());
    }

}
