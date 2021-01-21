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
        dd($data);
        $data = [];
        $data['aaa'] = collect($array)->sum('aaa');
        $data['bbb'] = collect($array)->sum('bbb');
        $data['ccc'] = collect($array)->sum('ccc');
        dd($data);
    }

    public function broadcast()
    {
        $user = User::find(1);
        $message = 'hello,world!';
        $groupId = 0;
        event(new UserSendMessage($user, $message, $groupId));
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
}
