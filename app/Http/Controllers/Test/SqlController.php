<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/4/25
 * Time: 12:19 上午
 */


namespace App\Http\Controllers\Test;


use App\Http\Controllers\Core\Controller;
use App\Models\DownloadLog;


/**
 * 一些有趣的sql写法
 * Class SqlController
 * @package App\Http\Controllers\Test
 */
class SqlController extends Controller
{
    public function database()
    {
        // 原生查询 (数据没有被过滤)
        // select 始终返回 array，array 中的每个结果都是 php stdClass 对象
//        $user = DB::select('select * from users where id = ?', [1]);

//        DB::insert('insert into table_name () values (?, ?)', []);

//        DB::update("update table_name set column_name = 'a' where column_name2 = ?", []);

//        DB::delete("delete from table_name where id = ?", []);

//        DB::unprepared("update table_name set column_name = 'a' where column_name2 = 'Dries'");

        // 事务
        /*DB::transaction(function () {
            DB::update('update users set votes = 1');

            DB::delete('delete from posts');
        }, 3);
        DB::beginTransaction();
        DB::rollBack();
        DB::commit();*/

        // 有趣的查询构造
        /*DB::table('users')->orderBy('id')->chunkById(2, function ($users) {
            foreach ($users as $user) {
                var_dump($user);
            }
        }, 'id');*/

        /*$query = DB::table('users')->select('id');
        $users = $query->addSelect('name')->get()->toArray();   // 推荐用法
        $users = $query->selectRaw('name')->get()->toArray(); */

        /*DB::table('users')->selectRaw('price * ? as price_with_tax', [1.0825])
            ->whereRaw('price > IF(state = "TX", ?, 100)', [200])
            ->groupBy('department')
            ->havingRaw('SUM(price) > ?', [2500])
            ->orderByRaw('updated_at - created_at DESC')
            ->groupByRaw('city, state')
            ->get();*/

        /*$latestDownloadLogs = DB::table('download_log')
            ->select(['file_name', 'creator_id']);
        $users = DB::table('users')
            ->leftJoinSub($latestDownloadLogs, 'd_log', function ($join) {
                $join->on('users.id', '=', 'd_log.creator_id');
            })->get();*/
        //select * from `users` left join (select `file_name`, `creator_id` from `download_log`) as `d_log` on `users`.`id` = `d_log`.`creator_id`

        /*$users = User::where(function ($query) {
            $query->select('creator_id')
                ->from('download_log', 'd_log')
                ->whereColumn('d_log.creator_id', 'users.id')
                ->orderByDesc('d_log.created_at')
                ->limit(1);
        }, '=', 1)->get();*/
        //select * from `users` where (select `creator_id` from `download_log` as `d_log` where `d_log`.`creator_id` = `users`.`id` order by `d_log`.`created_at` desc limit 1) = '1'

        /*$users = DB::table('users')
            ->fromSub(DB::table('download_log')->select(['file_name', 'creator_id']), 'a')
            ->when(1, function ($query) {
                return $query->where('id', '=', 1);
            })->dump();*/
        //select * from (select `file_name`, `creator_id` from `download_log`) as `a` where `id` = ?

        var_dump(111222333);
    }

    public function orm()
    {
        // collection chunk cursor

        // 动态查询作用域
//        $dLog = DownloadLog::creator('1')->first();

        // 批量不能触发事件
//        User::where('id', '=', 1)->update(['name' => 'zs666']);

        // 单条可以触发
        /*$user = User::where('id', '=', 1)->first();
        $user->name = 'zs6667';
        $user->update();*/

        var_dump(111222333);
    }

    public function relationships()
    {
        // 一对一$user
//        $user = User::select(['id', 'name'])->find(2)->downloadLog;
        /*select `id`, `name` from `users` where `users` . `id` = '1' limit 1
        select * from `download_log` where `download_log` . `creator_id` = '1' and `download_log` . `creator_id` is not null limit 1*/

        // 一对一 (belongs)
//        $downloadLog = DownloadLog::select(['id', 'file_name', 'creator_id'])->find(2)->user;
        /*select `id`, `file_name`, `creator_id` from `download_log` where `download_log`.`id` = '1' limit 1
        select * from `users` where `users`.`id` = '1' limit 1 */

        // 一对多
//        $user = User::select(['id', 'name'])->find(1)->downloadLogs;
//        foreach ($downloadLogs as $downloadLog) {
//            echo $downloadLog->file_name;
//        }
        /*select `id`, `name` from `users` where `users`.`id` = '1' limit 1
        select * from `download_log` where `download_log`.`creator_id` = '1' and `download_log`.`creator_id` is not null*/

        // 一对多 (关联查询)
//        $user = User::select(['id', 'name'])->find(1)->downloadLogs()->select('file_name')->where('id', '=', 2)->get();
        /*select `id`, `name` from `users` where `users`.`id` = '1' limit 1
        select `file_name` from `download_log` where `download_log`.`creator_id` = '1' and `download_log`.`creator_id` is not null and `id` = '2'*/

        // 一对多 (belongs) 和一对一基本一样

        // 渴求式加载
//        $user = User::with('downloadLogs')->get();
        /*select * from `users`
        select * from `download_log` where `download_log`.`creator_id` in (1, 2)*/

//        $user = User::with('downloadLogs:file_name,file_type,creator_id')->get()->toArray();
        /*select * from `users`
        select `file_name`, `file_type`, `creator_id` from `download_log` where `download_log`.`creator_id` in (1, 2)*/

//        $user = User::with(['downloadLogs' => function ($query) {
//            // limit 不应该在渴求式条件约束中使用（限制条数的话，会优先满足上面的用户，可能与预期结果不符合）
//            $query->select(['file_name', 'creator_id'])->limit(1);
//        }])->limit(1)->get()->toArray();

        // 不建议使用关联进行 创建 和 更新 操作
    }

    // chunk 连表使用
    public function chunk()
    {
        DownloadLog::from("download_log", "a")
            ->leftJoin("failed_jobs as b", "a.id", '=', "b.id")
            ->selectRaw("a.id as `a.id`, a.class_name, a.file_name, b.uuid, b.queue")
            ->where("a.id", '<', 4)
            ->chunkById(1, function ($data) {
                $data = $data->toArray();
               var_dump($data);
            }, "a.id");
    }


}
