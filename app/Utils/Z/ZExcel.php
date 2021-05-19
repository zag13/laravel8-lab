<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/18
 * Time: 5:08 下午
 */


namespace App\Utils\Z;


use App\Jobs\ExportJob;
use App\Models\DownloadLog;
use App\Utils\Singletons\SpreadsheetSingleton;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class ZExcel
{
    static protected $type = [
        'browser' => [
            1   // 输出到浏览器
        ],
        'file' => [
            2,  // 异步下载至服务器
            3,  // 分步数据导出 单例模式
            4,  // 分步数据导出 appendWrite
        ]
    ];

    /**
     * 通过 url 读取 excel 内容
     * @param $fileUrl
     * @return array
     */
    public static function readExcelByUrl($fileUrl)
    {
        if (empty($fileUrl)) trigger_error('url 不能为空');

        $fileFullName = array_reverse(explode('/', $fileUrl))[0];
        $fileType = array_reverse(explode('.', $fileFullName))[0];

        $tmp = 'uploads/excel/temp/' . uniqid() . '.' . $fileType;
        $filePath = ZFile::storageByUrl($fileUrl, $tmp);

        $data = self::readExcelByPath($filePath);

        unlink($filePath);

        return $data;
    }

    /**
     * 通过 本地路径 读取 excel 内容
     * @param $filePath
     * @return array
     */
    public static function readExcelByPath($filePath)
    {
        $filePath = storage_path('app/') . $filePath;
        if (!is_file($filePath)) trigger_error('文件不存在');

        $fileFullName = array_reverse(explode('/', $filePath))[0];
        $fileType = array_reverse(explode('.', $fileFullName))[0];

        $reader = IOFactory::createReader(ucfirst(strtolower($fileType)));
        $reader->setReadDataOnly(TRUE);
        $spreadsheet = $reader->load($filePath);
        $worksheet = $spreadsheet->getActiveSheet();

        // Get the highest row number and column letter referenced in the worksheet
        $highestRow = $worksheet->getHighestRow();
        $highestColumn = $worksheet->getHighestColumn();
        // Increment the highest column letter
        $highestColumn++;

        if ($highestRow == 0) trigger_error('Excel表格中没有数据');

        $data = [];

        for ($row = 1; $row <= $highestRow; ++$row) {
            for ($col = 'A'; $col != $highestColumn; ++$col) {
                $data[($row - 1)][] = $worksheet->getCell($col . $row)->getValue();
            }
        }

        return $data;
    }

    /**
     * 添加到导出队列
     * @param $params
     * @return void
     * @throws \Throwable
     */
    public static function add2Queue($params)
    {
        $exportType = $params['exportType'] ?? null;
        if (php_sapi_name() == 'cli' || !in_array($exportType, self::$type['file'])) return;

        DB::beginTransaction();
        try {
            $user = Auth::user();

            $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

            $data = [
                'class_name' => $backtrace[1]['class'],
                'action_name' => $backtrace[1]['function'],
                'params' => json_encode($params),
                'creator_id' => $user['id'],
                'creator_name' => $user['name'],
                'status' => 0
            ];

            $downloadLog = DownloadLog::create($data);
            ExportJob::dispatch($downloadLog);
        } catch (\Throwable $throwable) {
            DB::rollBack();
            throw new \Exception('加入下载列表失败：' . $throwable->getMessage());
        }
        DB::commit();

        throw new \Exception('加入下载列表成功', '10000');
    }

    /**
     * 通用导出
     * @param        $header
     * @param        $data
     * @param array $extra
     * @return array|bool|void
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public static function export($header, $data, array $extra = [])
    {
        // 重新格式化参数
        $params = [
            'exportType' => $extra['exportType'] ?? 1,
            'fileName' => $extra['fileName'] ?? '默认文件名',
            'fileType' => $extra['fileType'] ?? 'Csv'
        ];

        $legalType = array_reduce(self::$type, 'array_merge', []);
        if (!in_array($params['exportType'], $legalType)) trigger_error('导出类型不合法');
        if (in_array($params['exportType'], self::$type['browser']) && php_sapi_name() == 'cli') trigger_error('应当在在浏览器环境下运行');
        if (in_array($params['exportType'], self::$type['file']) && php_sapi_name() != 'cli') trigger_error('应当在在命令行环境下运行');

        switch ($params['exportType']) {
            case 1:
                self::export2Browser($header, $data, $params);
                break;
            case 2:
                return self::export2Local($header, $data, $params);
            case 3:
                $params = array_merge($params, [
                    'scene' => 'singleton',
                    'offset' => $extra['offset'] ?? 0,
                    'limit' => $extra['limit'] ?? 300
                ]);
                return self::export2Local($header, $data, $params);
            case 4:
                $params = array_merge($params, [
                    'downloadLogId' => $extra['downloadLogId'] ?? 0,
                    'offset' => $extra['offset'] ?? 0,
                    'isLast' => $extra['isLast'] ?? true
                ]);
                return self::appendWrite($header, $data, $params);
        }

        return true;
    }

    /**
     * 输出到浏览器
     * @param $header
     * @param $data
     * @param $fileName
     * @param $fileType
     */
    private static function export2Browser($header, $data, array $extra = [])
    {
        $fileName = $extra['fileName'];
        $fileType = $extra['fileType'];

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
     * @param array $extra
     * @return array|bool
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private static function export2Local($header, $data, array $extra = [])
    {
        $params = [
            'offset' => $extra['offset'] ?? 0,
            'scene' => $extra['scene'] ?? 'basic'
        ];

        $spreadsheet = self::exportBasic($header, $data, $params);

        if ($params['scene'] == 'singleton' && count($data) == $extra['limit']) return true;

        $fileName = $extra['fileName'];
        $fileType = $extra['fileType'];

        $fileInfo = self::save2File($spreadsheet, $fileType);

        unset($spreadsheet);

        return [
            'fileName' => $fileName,
            'fileType' => $fileType,
            'fileSize' => $fileInfo['fileSize'],
            'fileLink' => $fileInfo['filePath']
        ];
    }

    // 利用 追加写 和 total 的导出
    private static function appendWrite($header, $data, array $extra = [])
    {
        $fileLink = $extra['file_link'];
        // 对已有 excel 文件进行追加写
        if ($fileLink) {
            return true;
        }

        // 首次生成 excel 文件

        return true;
    }

    /**
     * 生成 excel 文件
     * @param $spreadsheet
     * @param $fileType
     * @return array
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    private static function save2File($spreadsheet, $fileType)
    {
        $writer = IOFactory::createWriter($spreadsheet, $fileType);

        $date = date('Y-m-d');
        $path = storage_path('app/download/excel/' . $date . '/');
        if (!is_dir($path)) {
            Storage::makeDirectory('download/excel/' . date('Y-m-d') . '/');
        }

        $tmp = uniqid() . '.' . strtolower($fileType);
        $writer->save($path . $tmp);

        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $writer);

        return [
            'filePath' => 'download/excel/' . $date . '/' . $tmp,
            'fileSize' => Storage::size('download/excel/' . $date . '/' . $tmp)
        ];
    }

    /**
     * 导出基本设置
     * @param $header
     * @param $data
     * @param array $extra
     * @return Spreadsheet
     */
    private static function exportBasic($header, $data, $extra = [])
    {
        $scene = $extra['scene'] ?? 'basic';
        $offset = $extra['offset'] ?? 0;

        if ($scene == 'singleton') {
            $spreadsheet = SpreadsheetSingleton::getInstance($offset);
        } else {
            $spreadsheet = new Spreadsheet();
        }

        $sheet = $spreadsheet->getActiveSheet();

        if ($offset == 0) {
            $sheet = $sheet->setTitle('工作表格1');

            $col = 1;
            foreach ($header as $value) {
                $sheet->setCellValueByColumnAndRow($col, 1, $value);
                $col++;
            }
            unset($col);
        }

        $row = $offset + 2;
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
    private static function setHeader($fileName, $fileType)
    {
        $type = ['Xlsx', 'Xls', 'Csv'];

        if (!in_array($fileType, $type)) trigger_error('未知文件类型');

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
