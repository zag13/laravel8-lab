<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/18
 * Time: 5:08 下午
 */


namespace App\Utils\Z;


use App\Jobs\ExportJob;
use App\Models\DownloadLogModel;
use App\Utils\Single\SingleSpreadsheet;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ZExcel
{
    /**
     * 添加到导出队列（限制为1000条）
     * @param $params
     */
    public static function add2Queue($params)
    {
        if (!in_array($params['exportType'], config('appointment.exportType.local')) || php_sapi_name() == 'cli') return false;

        DB::beginTransaction();
        try {
            $user = Auth::user();
            if (isset($params['limit'])) $params['limit'] = 1000;
            if (isset($params['offset'])) $params['offset'] = 0;

            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $data = [
                'class_name' => $backtrace[1]['class'],
                'action_name' => $backtrace[1]['function'],
                'params' => json_encode($params),
                'creator_id' => $user['id'],
                'creator_name' => $user['name'],
                'status' => 0
            ];
            $downloadLog = DownloadLogModel::create($data);
            ExportJob::dispatch($downloadLog);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw new \Exception('加入下载列表失败：' . $throwable->getMessage());
        }
        DB::commit();

        // send 方法返回前端后，后续代码依旧运行，
        // 所以要在控制器强制返回而不是使用 send 方法
        return response()->json([
            'code' => 10000,
            'msg' => '加入下载列表成功'
        ]);
        // 也可以用这种方法来终止后续代码的运行，但是感觉不爽
        // throw new \Exception('加入下载列表成功','10000');
    }

    /**
     * 通用导出
     * @param        $header
     * @param        $data
     * @param        $fileName
     * @param string $fileType
     * @param int $downloadType
     * @return array|false
     */
    public static function export($header, $data, $extra = [])
    {
        $extra = [
            'exportType' => $extra['exportType'] ?? 1,
            'fileName' => $extra['fileName'] ?? '默认文件名',
            'fileType' => $extra['fileType'] ?? 'Csv',
            'downloadLogId' => $extra['downloadLogId'] ?? 0
        ];
        $exportType = array_reduce(config('appointment.exportType'), 'array_merge', []);
        if (!in_array($extra['exportType'], $exportType)) return false;

        switch ($extra['exportType']) {
            // 导出至浏览器
            case 1:
            default:
                self::export2Browser($header, $data, $extra);
                break;
            // 导出至服务器
            case 2:
                return self::export2Local($header, $data, $extra);
            // 大数据导出至服务器
            case 3:
                return self::bigDataExport2Local($header, $data, $extra);
        }
    }

    /**
     * 输出到浏览器
     * @param $header
     * @param $data
     * @param $fileName
     * @param $fileType
     */
    private static function export2Browser($header, $data, $extra = [])
    {
        $fileName = $extra['fileName'] ?? 'aaa';
        $fileType = $extra['fileType'] ?? 'Csv';

        $spreadsheet = self::exportBasic($header, $data);

        $writer = IOFactory::createWriter($spreadsheet, $fileType);
        self::setHeader($fileName, $fileType);

        $writer->save('php://output');
        $spreadsheet->disconnectWorksheets();

        unset($spreadsheet, $writer);
    }

    /**
     * 下载到服务器
     * @param $header
     * @param $data
     * @param $fileName
     * @param $fileType
     */
    private static function export2Local($header, $data, $extra = [])
    {
        $fileName = $extra['fileName'] ?? '默认文件名';
        $fileType = $extra['fileType'] ?? 'Csv';

        $spreadsheet = self::exportBasic($header, $data);

        $writer = IOFactory::createWriter($spreadsheet, $fileType);

        $path = storage_path('app/download/excel/' . date('Y-m-d') . '/');
        if (!is_dir($path)) {
            Storage::makeDirectory('download/excel/' . date('Y-m-d') . '/');
        }
        $tmp = uniqid() . '.' . strtolower($fileType);
        $writer->save($path . $tmp);
        $fileSize = Storage::size('download/excel/' . date('Y-m-d') . '/' . $tmp);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $writer);

        return [
            'fileName' => $fileName,
            'fileType' => $fileType,
            'fileSize' => $fileSize,
            'fileLink' => 'download/excel/' . date('Y-m-d') . '/' . $tmp
        ];
    }

    /**
     * 1000 条以上的导出
     * @param $header
     * @param $data
     * @param array $extra
     * @return \Illuminate\Http\JsonResponse
     */
    private static function bigDataExport2Local($header, $data, $extra = [])
    {
        if (empty($extra['downloadLogId'])) trigger_error('downloadLogId 不能为空');

        $fileType = $extra['fileType'] ?? 'Csv';

        $extra['i'] = $extra['i'] ?? 0;
        $extra['nums'] = $extra['nums'] ?? 300;

        $spreadsheet = self::bigDataExportBasic($header, $data, $extra);

        // 判断是否为最后一次
        if (count($data) == $extra['nums']) return true;

        $writer = IOFactory::createWriter($spreadsheet, $fileType);

        $path = storage_path('app/download/excel/' . date('Y-m-d') . '/');
        if (!is_dir($path)) {
            Storage::makeDirectory('download/excel/' . date('Y-m-d') . '/');
        }

        $tmp = uniqid() . '.' . strtolower($fileType);
        $writer->save($path . $tmp);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $writer);

        $fileName = $extra['fileName'] ?? '默认文件名';
        $fileSize = Storage::size('download/excel/' . date('Y-m-d') . '/' . $tmp);
        $downloadLogId = $extra['downloadLogId'];

        DownloadLogModel::where('id', '=', $downloadLogId)
            ->update([
                'file_name' => $fileName,
                'file_type' => $fileType,
                'file_size' => $fileSize,
                'file_link' => 'download/excel/' . date('Y-m-d') . '/' . $tmp,
                'status' => 1
            ]);

        return response()->json([
            'code' => 10000,
            'msg' => '加入下载列表成功'
        ]);
    }

    /**
     * 导出基本设置
     * @param $header
     * @param $data
     * @return Spreadsheet
     */
    private static function exportBasic($header, $data)
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet()->setTitle('工作表格1');

        $col = 1;
        foreach ($header as $value) {
            $sheet->setCellValueByColumnAndRow($col, 1, $value);
            $col++;
        }
        unset($col);

        $row = 2;
        $header_key = array_keys($header);
        foreach ($data as $cols) {
            for ($col = 1; $col <= count($cols); $col++) {
                $sheet->setCellValueByColumnAndRow($col, $row, $cols[$header_key[$col - 1]]);
            }
            $row++;
        }
        unset($row);

        return $spreadsheet;
    }

    /**
     * 大数据导出基本设置
     * 这种利用单例模式将结果临时缓存起来，一定程度上减少了数据库和内存压力，但还可以继续优化
     * @param $header
     * @param $data
     * @param $extra
     * @return Spreadsheet
     */
    private static function bigDataExportBasic($header, $data, $extra)
    {
        $i = $extra['i'] ?? 0;
        $nums = $extra['nums'] ?? 300;

        $spreadsheet = SingleSpreadsheet::getInstance();
        $sheet = $spreadsheet->getActiveSheet();

        if ($i == 0) {
            $sheet = $sheet->setTitle('工作表格1');

            $col = 1;
            foreach ($header as $value) {
                $sheet->setCellValueByColumnAndRow($col, 1, $value);
                $col++;
            }
            unset($col);
        }

        $row = ($i * $nums) + 2;
        $header_key = array_keys($header);
        foreach ($data as $cols) {
            for ($col = 1; $col <= count($cols); $col++) {
                $sheet->setCellValueByColumnAndRow($col, $row, $cols[$header_key[$col - 1]]);
            }
            $row++;
        }
        unset($row);

        return $spreadsheet;
    }

    /**
     * 设置header头
     * @param string $fileName
     * @param string $fileType
     */
    private static function setHeader($fileName = '默认文件名', $fileType = 'Csv')
    {
        $type = ['Xlsx', 'Xls', 'Csv'];

        if (!in_array($fileType, $type)) trigger_error('未知文件类型', E_USER_ERROR);

        switch ($fileType) {
            case 'Csv':
                header('Content-Type: text/csv');
                header('Content-Disposition: attachment;filename=' . $fileName . '.csv');
                header('Cache-Control: max-age=0');
                break;
            case 'Xls':
                header('Content-Type: application/vnd.ms-excel');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xls');
                header('Cache-Control: max-age=0');
                break;
            case 'Xlsx':
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename=' . $fileName . '.xlsx');
                header('Cache-Control: max-age=0');
                break;
        }
    }
}
