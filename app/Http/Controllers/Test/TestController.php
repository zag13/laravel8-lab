<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/17
 * Time: 2:31 下午
 */


namespace App\Http\Controllers\Test;


use App\Events\UserSendMessage;
use App\Http\Controllers\Core\Controller;
use App\Models\DownloadLogModel;
use App\Models\TestEsModel;
use App\Models\UserModel;
use App\Utils\Es\MySearchRule;
use App\Utils\Z\ZExcel;
use App\Utils\Z\ZFile;
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Reader\Csv;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class TestController extends Controller
{
    public function user(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $params = request()->all();

        $data = UserModel::where('id', '=', $params['id'])->first()->toArray();

        return response()->json($data);
    }

    public function fileReader()
    {
        $fileUrl = "...";

        $fileFullName = array_reverse(explode('/', $fileUrl))[0];
        $fileType = array_reverse(explode('.', $fileFullName))[0];

        switch (strtoupper($fileType)) {
            case 'XLSX':
                $reader = new Xlsx();
                break;
            case 'XLS':
                $reader = new Xls();
                break;
            case 'CSV':
                $reader = new Csv();
                break;
            default:
                return $this->respFail('文件格式错误');
        }

        $tmp = 'uploads/excel/' . uniqid() . '.' . $fileType;
        $filePath = ZFile::storageByUrl($fileUrl, $tmp);

        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();
        $highestRow = $worksheet->getHighestRow();      // 最大行数（可以分批处理）

        if ($highestRow - 1 <= 0) {
            return $this->respFail('Excel表格中没有数据');
        }
        $data = [];
        for ($row = 2; $row <= $highestRow; $row++) {
            // 将 excel 数据存储到 数组 中
            $data[] = [
                '' => $worksheet->getCellByColumnAndRow(1, $row)->getFormattedValue(),
                '' => $worksheet->getCellByColumnAndRow(3, $row)->getFormattedValue(),
                '' => $worksheet->getCellByColumnAndRow(4, $row)->getFormattedValue(),
                '' => $worksheet->getCellByColumnAndRow(5, $row)->getFormattedValue(),
                '' => $worksheet->getCellByColumnAndRow(6, $row)->getFormattedValue(),
                '' => $worksheet->getCellByColumnAndRow(7, $row)->getFormattedValue(),
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

        ZExcel::export($header, $data, 'aaa');
    }

    public function queue(Request $request)
    {
        $this->validate($request, [
            'download' => 'integer|in:1,2'
        ]);

        $params = $request->all();

        $result = ZExcel::add2Queue($params);
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

        $result = ZExcel::export($header, $data, '测试文件', 'Csv', $params['download']);
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
        $downloadLog = DownloadLogModel::where('id', '=', $id)->firstOrFail();
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

        $data3 = 'a:55:{i:0;a:3:{s:5:"label";s:21:"子包活跃登录数";s:4:"prop";s:20:"adl_activeLoginCount";s:8:"disabled";b:1;}i:1;a:3:{s:5:"label";s:24:"子包活跃充值人数";s:4:"prop";s:20:"adl_userPaymentCount";s:8:"disabled";b:0;}i:2;a:3:{s:5:"label";s:24:"子包活跃充值金额";s:4:"prop";s:17:"adl_moneyPayCount";s:8:"disabled";b:0;}i:3;a:3:{s:5:"label";s:17:"子包活跃ARPPU";s:4:"prop";s:20:"adl_activeLoginARPPU";s:8:"disabled";b:0;}i:4;a:3:{s:5:"label";s:9:"激活数";s:4:"prop";s:13:"activateCount";s:8:"disabled";b:0;}i:5;a:3:{s:5:"label";s:21:"激活到登录界面";s:4:"prop";s:18:"activateLoginCount";s:8:"disabled";b:0;}i:6;a:3:{s:5:"label";s:21:"子包激活注册率";s:4:"prop";s:17:"adl_regActiveRate";s:8:"disabled";b:0;}i:7;a:3:{s:5:"label";s:9:"创角数";s:4:"prop";s:9:"roleCount";s:8:"disabled";b:0;}i:8;a:3:{s:5:"label";s:21:"子包注册创角率";s:4:"prop";s:15:"adl_regRoleRate";s:8:"disabled";b:0;}i:9;a:3:{s:5:"label";s:18:"子包注册人数";s:4:"prop";s:12:"adl_regCount";s:8:"disabled";b:0;}i:10;a:3:{s:5:"label";s:24:"子包注册排重设备";s:4:"prop";s:18:"adl_regDeviceCount";s:8:"disabled";b:0;}i:11;a:3:{s:5:"label";s:24:"子包注册充值人数";s:4:"prop";s:23:"adl_newUserPaymentCount";s:8:"disabled";b:0;}i:12;a:3:{s:5:"label";s:24:"子包注册充值金额";s:4:"prop";s:20:"adl_newMoneyPayCount";s:8:"disabled";b:0;}i:13;a:3:{s:5:"label";s:19:"子包注册数ARPU";s:4:"prop";s:16:"adl_regCountARPU";s:8:"disabled";b:0;}i:14;a:3:{s:5:"label";s:20:"子包注册数ARPPU";s:4:"prop";s:17:"adl_regCountARPPU";s:8:"disabled";b:0;}i:15;a:3:{s:5:"label";s:18:"子包注册次留";s:4:"prop";s:11:"adl_staySec";s:8:"disabled";b:0;}i:16;a:3:{s:5:"label";s:21:"母包活跃登录数";s:4:"prop";s:21:"game_activeLoginCount";s:8:"disabled";b:0;}i:17;a:3:{s:5:"label";s:24:"母包活跃充值人数";s:4:"prop";s:21:"game_userPaymentCount";s:8:"disabled";b:0;}i:18;a:3:{s:5:"label";s:24:"母包活跃充值金额";s:4:"prop";s:18:"game_moneyPayCount";s:8:"disabled";b:0;}i:19;a:3:{s:5:"label";s:17:"母包活跃ARPPU";s:4:"prop";s:21:"game_activeLoginARPPU";s:8:"disabled";b:0;}i:20;a:3:{s:5:"label";s:21:"母包激活注册率";s:4:"prop";s:18:"game_regActiveRate";s:8:"disabled";b:0;}i:21;a:3:{s:5:"label";s:21:"母包注册创角率";s:4:"prop";s:16:"game_regRoleRate";s:8:"disabled";b:0;}i:22;a:3:{s:5:"label";s:18:"母包注册人数";s:4:"prop";s:13:"game_regCount";s:8:"disabled";b:0;}i:23;a:3:{s:5:"label";s:24:"母包注册排重设备";s:4:"prop";s:19:"game_regDeviceCount";s:8:"disabled";b:0;}i:24;a:3:{s:5:"label";s:24:"母包注册充值人数";s:4:"prop";s:24:"game_newUserPaymentCount";s:8:"disabled";b:0;}i:25;a:3:{s:5:"label";s:24:"母包注册充值金额";s:4:"prop";s:21:"game_newMoneyPayCount";s:8:"disabled";b:0;}i:26;a:3:{s:5:"label";s:19:"母包注册数ARPU";s:4:"prop";s:17:"game_regCountARPU";s:8:"disabled";b:0;}i:27;a:3:{s:5:"label";s:20:"母包注册数ARPPU";s:4:"prop";s:18:"game_regCountARPPU";s:8:"disabled";b:0;}i:28;a:3:{s:5:"label";s:18:"母包注册次留";s:4:"prop";s:12:"game_staySec";s:8:"disabled";b:0;}i:29;a:3:{s:5:"label";s:21:"游戏活跃登录数";s:4:"prop";s:19:"cp_activeLoginCount";s:8:"disabled";b:0;}i:30;a:3:{s:5:"label";s:24:"游戏活跃充值人数";s:4:"prop";s:19:"cp_userPaymentCount";s:8:"disabled";b:0;}i:31;a:3:{s:5:"label";s:24:"游戏活跃充值金额";s:4:"prop";s:16:"cp_moneyPayCount";s:8:"disabled";b:0;}i:32;a:3:{s:5:"label";s:17:"游戏活跃ARPPU";s:4:"prop";s:19:"cp_activeLoginARPPU";s:8:"disabled";b:0;}i:33;a:3:{s:5:"label";s:21:"游戏激活注册率";s:4:"prop";s:16:"cp_regActiveRate";s:8:"disabled";b:0;}i:34;a:3:{s:5:"label";s:21:"游戏注册创角率";s:4:"prop";s:14:"cp_regRoleRate";s:8:"disabled";b:0;}i:35;a:3:{s:5:"label";s:18:"游戏注册人数";s:4:"prop";s:11:"cp_regCount";s:8:"disabled";b:0;}i:36;a:3:{s:5:"label";s:24:"游戏注册排重设备";s:4:"prop";s:17:"cp_regDeviceCount";s:8:"disabled";b:0;}i:37;a:3:{s:5:"label";s:24:"游戏注册充值人数";s:4:"prop";s:22:"cp_newUserPaymentCount";s:8:"disabled";b:0;}i:38;a:3:{s:5:"label";s:24:"游戏注册充值金额";s:4:"prop";s:19:"cp_newMoneyPayCount";s:8:"disabled";b:0;}i:39;a:3:{s:5:"label";s:19:"游戏注册数ARPU";s:4:"prop";s:15:"cp_regCountARPU";s:8:"disabled";b:0;}i:40;a:3:{s:5:"label";s:20:"游戏注册数ARPPU";s:4:"prop";s:16:"cp_regCountARPPU";s:8:"disabled";b:0;}i:41;a:3:{s:5:"label";s:18:"游戏注册次留";s:4:"prop";s:10:"cp_staySec";s:8:"disabled";b:0;}i:42;a:3:{s:5:"label";s:21:"账号活跃登录数";s:4:"prop";s:19:"ac_activeLoginCount";s:8:"disabled";b:0;}i:43;a:3:{s:5:"label";s:24:"账号活跃充值人数";s:4:"prop";s:19:"ac_userPaymentCount";s:8:"disabled";b:0;}i:44;a:3:{s:5:"label";s:24:"账号活跃充值金额";s:4:"prop";s:16:"ac_moneyPayCount";s:8:"disabled";b:0;}i:45;a:3:{s:5:"label";s:17:"账号活跃ARPPU";s:4:"prop";s:19:"ac_activeLoginARPPU";s:8:"disabled";b:0;}i:46;a:3:{s:5:"label";s:21:"账号激活注册率";s:4:"prop";s:16:"ac_regActiveRate";s:8:"disabled";b:0;}i:47;a:3:{s:5:"label";s:21:"账号注册创角率";s:4:"prop";s:14:"ac_regRoleRate";s:8:"disabled";b:0;}i:48;a:3:{s:5:"label";s:18:"账号注册人数";s:4:"prop";s:11:"ac_regCount";s:8:"disabled";b:0;}i:49;a:3:{s:5:"label";s:24:"账号注册排重设备";s:4:"prop";s:17:"ac_regDeviceCount";s:8:"disabled";b:0;}i:50;a:3:{s:5:"label";s:24:"账号注册充值人数";s:4:"prop";s:22:"ac_newUserPaymentCount";s:8:"disabled";b:0;}i:51;a:3:{s:5:"label";s:24:"账号注册充值金额";s:4:"prop";s:19:"ac_newMoneyPayCount";s:8:"disabled";b:0;}i:52;a:3:{s:5:"label";s:19:"账号注册数ARPU";s:4:"prop";s:15:"ac_regCountARPU";s:8:"disabled";b:0;}i:53;a:3:{s:5:"label";s:20:"账号注册数ARPPU";s:4:"prop";s:16:"ac_regCountARPPU";s:8:"disabled";b:0;}i:54;a:3:{s:5:"label";s:18:"账号注册次留";s:4:"prop";s:10:"ac_staySec";s:8:"disabled";b:0;}}';
        $data3 = collect(unserialize($data3))->filter(function ($value) {
            if ($value['disabled'] == false) return $value;
        })->pluck('prop')->all();

        var_dump($data, $data2, $data3);
    }

    public function broadcast()
    {
        $user = UserModel::find(1);
        $message = 'hello,world!';
        event(new UserSendMessage($user, $message));
    }

    public function relationships()
    {
        // 一对一$user
//        $user = UserModel::select(['id', 'name'])->find(2)->downloadLog;
        /*select `id`, `name` from `users` where `users` . `id` = '1' limit 1
        select * from `download_log` where `download_log` . `creator_id` = '1' and `download_log` . `creator_id` is not null limit 1*/

        // 一对一 (belongs)
//        $downloadLog = DownloadLogModel::select(['id', 'file_name', 'creator_id'])->find(2)->user;
        /*select `id`, `file_name`, `creator_id` from `download_log` where `download_log`.`id` = '1' limit 1
        select * from `users` where `users`.`id` = '1' limit 1 */

        // 一对多
//        $user = UserModel::select(['id', 'name'])->find(1)->downloadLogs;
//        foreach ($downloadLogs as $downloadLog) {
//            echo $downloadLog->file_name;
//        }
        /*select `id`, `name` from `users` where `users`.`id` = '1' limit 1
        select * from `download_log` where `download_log`.`creator_id` = '1' and `download_log`.`creator_id` is not null*/

        // 一对多 (关联查询)
//        $user = UserModel::select(['id', 'name'])->find(1)->downloadLogs()->select('file_name')->where('id', '=', 2)->get();
        /*select `id`, `name` from `users` where `users`.`id` = '1' limit 1
        select `file_name` from `download_log` where `download_log`.`creator_id` = '1' and `download_log`.`creator_id` is not null and `id` = '2'*/

        // 一对多 (belongs) 和一对一基本一样

        // 渴求式加载
//        $user = UserModel::with('downloadLogs')->get();
        /*select * from `users`
        select * from `download_log` where `download_log`.`creator_id` in (1, 2)*/

//        $user = UserModel::with('downloadLogs:file_name,file_type,creator_id')->get()->toArray();
        /*select * from `users`
        select `file_name`, `file_type`, `creator_id` from `download_log` where `download_log`.`creator_id` in (1, 2)*/

//        $user = UserModel::with(['downloadLogs' => function ($query) {
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

        /*$users = UserModel::where(function ($query) {
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
//        $dLog = DownloadLogModel::creator('1')->first();

        // 批量不能触发事件
//        UserModel::where('id', '=', 1)->update(['name' => 'zs666']);

        // 单条可以触发
        /*$user = UserModel::where('id', '=', 1)->first();
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

    public function faker()
    {
        TestEsModel::factory(90)->create();
    }

    public function search(Request $request)
    {
        $q = $request->get('q');
        $paginator = [];
        if ($q) $paginator = TestEsModel::search($q)
            ->rule(MySearchRule::class)
            ->paginate(5);

//        dd($paginator);
        return view('search', compact('paginator', 'q'));
    }
}
