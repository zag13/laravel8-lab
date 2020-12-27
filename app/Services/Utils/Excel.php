<?php
/**
 * Created by PhpStorm
 * User: ZS
 * Date: 2020/12/18
 * Time: 5:08 下午
 */


namespace App\Services\Utils;


use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class Excel
{
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
        $tmp = $path . $fileName . '.' . strtolower($fileType);
        $writer->save($tmp);
        $spreadsheet->disconnectWorksheets();
        unset($spreadsheet, $writer);
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