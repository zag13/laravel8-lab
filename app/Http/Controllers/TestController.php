<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/17
 * Time: 2:31 下午
 */


namespace App\Http\Controllers;


use App\Events\UserSendMessage;
use App\Http\Controllers\Core\Controller;
use App\Models\ModDownloadLog;
use App\Models\User;
use App\Services\Utils\Excel;
use App\Services\Utils\File;
use Elasticsearch\ClientBuilder;
use GuzzleHttp\Ring\Client\MockHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpParser\Node\Expr\AssignOp\Mod;

class TestController extends Controller
{
    public function user(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $params = request()->all();

        $data = User::where('id', '=', $params['id'])->first()->toArray();

        return response()->json($data);
    }

    public function fileReader()
    {
        $fileUrl = "https://kyyx-dept1-store-test.oss-cn-hangzhou.aliyuncs.com/source/20201221/e761e7a20402d5a515964d20a016d930.xlsx";

        $fileFullName = array_reverse(explode('/', $fileUrl))[0];
        $fileType = array_reverse(explode('.', $fileFullName))[0];

        if (strtoupper($fileType) == 'XLSX') {
            $reader = new Xlsx();
        } elseif (strtoupper($fileType) == 'XLS') {
            $reader = new Xls();
        } elseif (strtoupper($fileType) == 'CSV') {
            $reader = new Csv();
        } else {
            $this->respFail('文件格式错误');
        }

        $tmp = 'uploads/excel/' . uniqid() . '.' . $fileType;
        $filePath = File::storageFromUrl($fileUrl, $tmp);

        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();      // 最大行数（可以分批处理）

        if ($highestRow - 1 <= 0) {
            $this->respFail('Excel表格中没有数据');
        }
        $data = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            // 将 excel 数据存储到 数组 中
            $data[] = [
                'date' => $worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue(),
                'id' => $worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue(),
                '消耗' => $worksheet->getCellByColumnAndRow(4, $row)->getFormattedValue(),
                '曝光数' => $worksheet->getCellByColumnAndRow(5, $row)->getFormattedValue(),
                '点击数' => $worksheet->getCellByColumnAndRow(6, $row)->getFormattedValue(),
                '渠道号' => $worksheet->getCellByColumnAndRow(7, $row)->getFormattedValue(),
            ];
        }

        rrmDir(dirname($filePath));

        // 处理数据
        var_dump($data);
    }

    public function fileExport()
    {
        $header = [
            'e' => '身高',
            'a' => '姓名',
            'c' => '学历',
            'd' => '年龄',
            'b' => '性别',
        ];
        $data = [
            [
                'c' => '专科',
                'b' => '男',
                'd' => '18',
                'a' => '小明',
                'e' => '175'
            ],
            [
                'd' => '18',
                'a' => '小红',
                'b' => '女',
                'c' => '本科',
                'e' => '155'
            ],
            [
                'a' => '小蓝',
                'b' => '男',
                'c' => '专科',
                'd' => '20',
                'e' => '170'
            ],
            [
                'a' => '张三',
                'b' => '男',
                'c' => '本科',
                'd' => '19',
                'e' => '165'
            ],
            [
                'a' => '李四',
                'b' => '男',
                'c' => '专科',
                'd' => '22',
                'e' => '175'
            ]
        ];

        Excel::export($header, $data, 'aaa');
    }

    public function queue(Request $request)
    {
        $this->validate($request, [
            'download' => 'integer|in:1,2'
        ]);

        $params = $request->all();

        $result = Excel::add2Queue($params);
        if (!empty($result)) return $result;

        $header = [
            'a' => '姓名',
            'b' => '性别',
            'c' => '学历',
            'd' => '年龄',
            'e' => '身高',
        ];
        $data = [
            [
                'a' => '小明',
                'c' => '专科',
                'd' => '18',
                'b' => '男',
                'e' => '175'
            ],
            [
                'b' => '女',
                'a' => '小红',
                'c' => '本科',
                'd' => '18',
                'e' => '155'
            ],
            [
                'd' => '20',
                'a' => '小蓝',
                'b' => '男',
                'c' => '专科',
                'e' => '170'
            ],
            [
                'a' => '张三',
                'b' => '男',
                'c' => '本科',
                'd' => '19',
                'e' => '165'
            ],
            [
                'a' => '李四',
                'b' => '男',
                'c' => '专科',
                'd' => '22',
                'e' => '175'
            ]
        ];

        $result = Excel::export($header, $data, '测试文件', 'Csv', $params['download']);
        if (!empty($result)) return $result;

        return $this->respSuccess($data, '正常查看信息');
    }

    public function download(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $id = $request->input('id');

        $user = Auth::user();
        $downloadLog = ModDownloadLog::where('id', '=', $id)->firstOrFail();
        if ($downloadLog['creator_id'] != $user['id']) throw new \Exception('你无权下载该数据');

        $fileFullName = $downloadLog['file_name'] . '.' . strtolower($downloadLog['file_type']);

        return Storage::download($downloadLog['file_link'], $fileFullName);
    }

    public function collect()
    {
        $array = [
            'a' => ['title' => 'a', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
            'b' => ['title' => 'b', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
            'c' => ['title' => 'c', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
            'd' => ['title' => 'a', 'aaa' => 1, 'bbb' => 2, 'ccc' => 3],
        ];
        $data = collect($array)->reduce(function ($result, $item) {
            if ($result == null) {
                $result = [
                    'aaa' => 0,
                    'bbb' => 0,
                    'ccc' => 0
                ];
            }
            $result['aaa'] += $item['aaa'];
            $result['bbb'] += $item['bbb'];
            $result['ccc'] += $item['ccc'];

            return $result;
        });

        $data2 = collect($array)->groupBy('title')
            ->map(function ($value, $groupBy) {
                return [
                    'title' => $groupBy,
                    'aaa' => $value->sum('aaa'),
                    'bbb' => $value->sum('bbb'),
                    'ccc' => $value->sum('ccc'),
                ];
            })
            ->toArray();

        var_dump($data, $data2);
    }

    public function broadcast()
    {
        $user = User::find(1);
        $message = 'hello,world!';
        event(new UserSendMessage($user, $message));
    }

    public function relationships()
    {
        // 一对一$user
//        $user = User::select(['id', 'name'])->find(2)->downloadLog;
        /*select `id`, `name` from `users` where `users` . `id` = '1' limit 1
        select * from `download_log` where `download_log` . `creator_id` = '1' and `download_log` . `creator_id` is not null limit 1*/

        // 一对一 (belongs)
//        $downloadLog = ModDownloadLog::select(['id', 'file_name', 'creator_id'])->find(2)->user;
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

//        var_dump($user);
//        var_dump(111);
    }

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
//        $dLog = ModDownloadLog::creator('1')->first();

        // 批量不能触发事件
//        User::where('id', '=', 1)->update(['name' => 'zs666']);

        // 单条可以触发
        /*$user = User::where('id', '=', 1)->first();
        $user->name = 'zs6667';
        $user->update();*/

        var_dump(111222333);
    }

    public function elasticsearch()
    {
        $client = ClientBuilder::create()
            ->setHosts(config('database.connections.elasticsearch.hosts'))
            ->build();

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id',
            'body' => ['testField' => 'abc']
        ];

        $response = $client->index($params);

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id'
        ];

        //$response = $client->get($params);

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'body' => [
                'query' => [
                    'match' => [
                        'testField' => 'abc'
                    ]
                ]
            ]
        ];

        //$response = $client->search($params);

        $params = [
            'index' => 'my_index',
            'type' => 'my_type',
            'id' => 'my_id'
        ];

        //$response = $client->delete($params);

//        $deleteParams = [
//            'index' => 'my_index'
//        ];
//        $response = $client->indices()->delete($deleteParams);

        // 索引相关
        //$client->indices();
        // 集群相关
        //$client->cluster();

        dd($response);
    }
}
