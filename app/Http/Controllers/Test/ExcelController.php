<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2021/5/3
 * Time: 12:04 上午
 */


namespace App\Http\Controllers\Test;


use App\Http\Controllers\Core\Controller;
use App\Models\DownloadLog;
use App\Models\TestEs;
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
            'exportType' => 'integer|in:2'
        ]);

        $params = $request->all();

        ZExcel::add2Queue($params);

        $testData = $this->testData();

        if (isset($params['exportType'])) {
            $result = ZExcel::export($testData['header'], $testData['data'], ['exportType' => 2]);
            if ($result) return $result;
        }

        return $this->respSuccess($testData['data'], '正常查看信息');
    }

    public function bigDataExport2(Request $request)
    {
        $this->validate($request, [
            'endId' => 'required|integer',
            'exportType' => 'integer|in:4'
        ]);

        $params = $request->all();

        $sql = TestEs::where('id', '<', $params['endId']);

        $total = $sql->count();

        $params['total'] = $total;
        ZExcel::add2Queue($params);

        $data = $sql->select(['id', 'name', 'phone', 'email', 'country', 'address', 'company'])
            ->offset($params['offset'] ?? 0)->limit($params['limit'] ?? 20)
            ->get()->toArray();

        if (isset($params['exportType'])) {
            $header = ['id' => 'ID', 'name' => '姓名', 'phone' => '电话', 'email' => '邮箱',
                'country' => '国家', 'address' => '地址', 'company' => '公司'];
            $extra = [
                'exportType' => $params['exportType'],
                'downloadLogId' => $params['downloadLogId'],
                'offset' => $params['offset'],
                'isLast' => $params['isLast']
            ];
            $result = ZExcel::export($header, $data, $extra);
            if ($result) return $result;
        }

        return $this->respSuccess([
            'total' => $total,
            'item' => $data
        ]);
    }

    public function bigDataExport4(Request $request)
    {
        $this->validate($request, [
            'endId' => 'required|integer',
            'exportType' => 'integer|in:6'
        ]);

        $params = $request->all();

        $sql = TestEs::where('id', '<', $params['endId']);

        $total = $sql->count();

        $params['total'] = $total;
        ZExcel::add2Queue($params);

        $data = $sql->select(['id', 'name', 'phone', 'email', 'country', 'address', 'company'])
            ->offset($params['offset'] ?? 0)->limit($params['limit'] ?? 20)
            ->get()->toArray();

        if (isset($params['exportType'])) {
            $header = ['id' => 'ID', 'name' => '姓名', 'phone' => '电话', 'email' => '邮箱',
                'country' => '国家', 'address' => '地址', 'company' => '公司'];
            $extra = [
                'exportType' => 6,
                'downloadLogId' => $params['downloadLogId'],
                'offset' => $params['offset'],
                'isLast' => $params['isLast']
            ];
            $result = ZExcel::export($header, $data, $extra);
            if ($result) return $result;
        }

        return $this->respSuccess([
            'total' => $total,
            'item' => $data
        ]);
    }

    public function download(Request $request)
    {
        $this->validate($request, [
            'id' => 'required|integer'
        ]);

        $id = $request->input('id');

        $user = Auth::user();
        $downloadLog = DownloadLog::where('id', '=', $id)->firstOrFail();
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
