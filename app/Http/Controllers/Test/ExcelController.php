<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/3
 * Time: 12:04 上午
 */


namespace App\Http\Controllers\Test;


use App\Http\Controllers\Core\Controller;
use App\Models\DownloadLogModel;
use App\Models\TestEsModel;
use App\Utils\Z\ZExcel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExcelController extends Controller
{
    public function readExcel()
    {
        $aaa = ZExcel::readExcelByPath('download/excel/2021-05-01/608cf76c3cd43.csv');
        print_r($aaa);
    }

    public function export2Browser()
    {
        $testData = $this->testData();

        ZExcel::export($testData['header'], $testData['data']);
    }

    public function export2Local(Request $request)
    {
        $this->validate($request, [
            'exportType' => 'integer|in:1,2'
        ]);

        $params = $request->all();

        $result = ZExcel::add2Queue($params);
        if (!empty($result)) return $result;

        $testData = $this->testData();

        $result = ZExcel::export($testData['header'], $testData['data'], ['exportType' => $params['exportType']]);
        if (!$result) return $result;

        return $this->respSuccess($testData['data'], '正常查看信息');
    }

    public function bigDataExport(Request $request)
    {
        $this->validate($request, [
            'exportType' => 'integer|in:0,3'
        ]);

        $params = $request->all();

        $result = ZExcel::add2Queue($params);
        if (!empty($result)) return $result;

        $i = 0;
        $data = TestEsModel::select(['id', 'name', 'phone', 'email', 'country', 'address', 'company'])
            ->where('id', '<', '30')
            ->when($params['exportType'] == 3, function ($query) use (&$i, $params) {
                return $query->chunkById(10, function ($data) use (&$i, $params) {
                    $data = $data->toArray();

                    $extra = [
                        'i' => $i,
                        'nums' => 10,
                        'exportType' => $params['exportType'],
                        'downloadLogId' => $params['downloadLogId'] ?? 23
                    ];

                    $header = ['id' => 'ID', 'name' => '姓名', 'phone' => '电话', 'email' => '邮箱',
                        'country' => '国家', 'address' => '地址', 'company' => '公司'];
                    ZExcel::export($header, $data, $extra);

                    $i++;
                }, 'id');
            }, function ($query) {
                return $query->offset(0)->limit(1)->get()->toArray();
            });

        if ($params['exportType'] == 3) return true;

        return $this->respSuccess($data);
    }

    public function bigDataExport2(Request $request)
    {

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

    private function testData()
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

        return [
            'header' => $header,
            'data' => $data
        ];
    }

}
