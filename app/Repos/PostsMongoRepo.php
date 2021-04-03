<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/4/1
 * Time: 6:18 下午
 */


namespace App\Repos;


use App\Models\Mongos\PostsMongo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class PostsMongoRepo
{
    protected $posts;

    public function __construct(PostsMongo $posts)
    {
        $this->posts = $posts;
    }

    /**
     * 通过 id 获取 postsMongo 模型
     * @param int $id
     * @param array|string[] $columns
     * @return PostsMongo
     */
    public function getById(string $id, array $columns = ['*'])
    {
        $cacheKey = 'posts_mongo_' . $id;
        return Cache::remember($cacheKey, 1 * 60 * 60, function () use ($id, $columns) {
            return $this->posts->find($id, $columns);
        });
    }

    /**
     * 通过 ids 获取 postsMongo 模型
     * @param array $ids
     * @param array|string[] $columns
     * @param callable|null $callback
     * @return PostsMongo
     */
    public function getByIds(array $ids, array $columns = ['*'], callable $callback = null)
    {
        $query = $this->posts->select($columns)->whereIn('_id', $ids);
        if ($callback) $query = $callback($query);
        return $query->get();
    }

    public function addViews(PostsMongo $postsMongo)
    {
        $res = $postsMongo->increment('views');
        if ($res) Redis::zincrby('posts_mongo_popular_', 1, $postsMongo->id);
        return $postsMongo->views;
    }

    public function trending($num = 10, $columns = ['*'])
    {
        $cacheKey = 'posts_mongo_popular_' . $num;
        return Cache::remember($cacheKey, 10 * 60, function () use ($num, $columns) {
            $postIds = Redis::zrevrange('posts_mongo_popular_', 0, $num - 1);
            if (!$postIds) return null;

            // mongo 没有 order by filed()
            // 1、用 redis 取出的 id 获取对应的数据后，通过排序字段进行排序
            // 2、取出相应数据用 PHP 进行排序（只适用于数据较少的情况）
            // 3、临时给每个 ID 一个权重（用 redis 中的顺序），在 mongo 用该权重进行排序
            return $this->getByIds($postIds, $columns, function ($query) {
                return $query->orderBy('view', 'asc');
            });
        });
    }
}
