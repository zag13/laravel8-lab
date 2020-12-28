<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/18
 * Time: 5:08 下午
 */


namespace App\Services\Utils;


use App\Jobs\ExcelDownload;
use App\Models\ModDownloadLog;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel
{
    public static function export($header, $data, $fileName, $fileType, $downloadType = 2)
    {
        if (!in_array($downloadType, config('appointment.download'))) return false;

        if ($downloadType == 1) {
            return self::export2Local($header, $data, $fileName, $fileType);
        } elseif ($downloadType == 2) {
            self::export2Browser($header, $data, $fileName, $fileType);
        }
    }

    /**
     * 输出到浏览器
     * @param $header
     * @param $data
     * @param $fileName
     * @param $fileType
     */
    public static function export2Browser($header, $data, $fileName, $fileType)
    {
        $spreadsheet = self::exportBase($header, $data);

        $writer = IOFactory::createWriter($spreadsheet, $fileType);
        self::excelBrowserExport($fileName, $fileType);
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
    public static function export2Local($header, $data, $fileName, $fileType)
    {
        if (!$fileName) trigger_error('文件名不能为空', E_USER_ERROR);

        $spreadsheet = self::exportBase($header, $data);

        $writer = IOFactory::createWriter($spreadsheet, $fileType);

        $path = storage_path('app/download/excel/');
        if (!is_dir($path)) {
            Storage::makeDirectory('download/excel/');
        }
        $tmp = uniqid() . '.' . strtolower($fileType);
        $writer->save($path . $tmp);
        $fileSize = Storage::size('download/excel/' . $tmp);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $writer);

        return [
            'fileName' => $fileName,
            'fileType' => $fileType,
            'fileSize' => $fileSize,
            'fileLink' => 'download/excel/' . $tmp
        ];
    }


    /**
     * 添加到下载队列（限制为1000条）
     * @param $params
     */
    public static function add2Queue($params)
    {
        if ($params['download'] != 1 || php_sapi_name() == 'cli') return true;

        try {
            $user = Auth::user();
            if (isset($params['limit'])) $params['limit'] = 1000;
            if (isset($params['offset'])) $params['offset'] = 0;

            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
            $data = [
                'class_name' => $backtrace[1]['class'],
                'action_name' => $backtrace[1]['function'],
                'params' => serialize($params),
                'creator_id' => $user['id'],
                'creator_name' => $user['name']
            ];
            $downloadLog = ModDownloadLog::create($data);
            ExcelDownload::dispatch($downloadLog)->onQueue('ExcelDownload')
                ->delay(Carbon::now()->addSeconds(10));
        } catch (\Throwable $throwable) {
            throw new \Exception('加入下载列表失败：' . $throwable->getMessage());
        }

        return response()->json([
            'code' => 10000,
            'msg' => '加入下载列表成功'
        ])->send();
    }

    /**
     * 导出基本设置
     * @param $header
     * @param $data
     * @return Spreadsheet
     */
    private static function exportBase($header, $data)
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
        foreach ($data as $cols) {
            $col = 1;
            foreach ($cols as $cellValue) {
                $sheet->setCellValueByColumnAndRow($col, $row, $cellValue);
                $col++;
            }
            $row++;
        }
        unset($row, $col);

        return $spreadsheet;
    }

    /**
     * 输出到浏览器——设置header头
     * @param string $fileName
     * @param string $fileType
     */
    private static function excelBrowserExport($fileName, $fileType)
    {
        if (!$fileName) trigger_error('文件名不能为空', E_USER_ERROR);

        $type = ['Xlsx', 'Xls', 'Csv'];

        if (!in_array($fileType, $type)) trigger_error('未知文件类型', E_USER_ERROR);

        if ($fileType == 'Xlsx') {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename=' . $fileName . '.xlsx');
            header('Cache-Control: max-age=0');
        } elseif ($fileType == 'Xls') {
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename=' . $fileName . '.xls');
            header('Cache-Control: max-age=0');
        } elseif ($fileType == 'Csv') {
            header('Content-Type: text/csv');
            header('Content-Disposition: attachment;filename=' . $fileName . '.csv');
            header('Cache-Control: max-age=0');
        }
    }
}