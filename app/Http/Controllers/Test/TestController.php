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
use Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    public function readExcel()
    {
        $aaa = ZExcel::readExcelByPath('download/excel/2021-05-01/608cf76c3cd43.csv');
        print_r($aaa);
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

        ZExcel::export($header, $data);
    }

    public function bigDataExport(Request $request)
    {
        $this->validate($request, [
            'exportType' => 'integer|in:0,3'
        ]);

        $params = $request->all();
        ZExcel::add2Queue($params);

        $export = $params['exportType'] ?? 0;

        $i = 0;
        $data = TestEsModel::select(['id', 'name', 'phone', 'email', 'country', 'address', 'company'])
            ->where('id', '<', '30')
            ->when($export == 0, function ($query) {

                return $query->offset(0)->limit(1)->get()->toArray();

            }, function ($query) use (&$i) {

                return $query->chunkById(10, function ($data) use (&$i) {
                    $header = ['id' => 'ID', 'name' => '姓名', 'phone' => '电话', 'email' => '邮箱', 'country' => '国家',
                        'address' => '地址', 'company' => '公司'];

                    $data = $data->toArray();

                    $extra = ['i' => $i, 'nums' => 10];

                    ZExcel::export($header, $data, $extra);

                    $i++;
                }, 'id');

            });

        print_r($data);
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

        $result = ZExcel::export($header, $data);
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
