<?php
/**
 * Created by ogenes-permission.
 * User: ogenes
 * Date: 2022/5/3
 */

namespace App\Helpers;


use App\Exceptions\CommonException;
use App\Exceptions\ErrorCode;
use Download;
use Illuminate\Support\Facades\Facade;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use RuntimeException;

class ExcelHelper extends Facade
{
    public const TMP_PATH = 'excels/';
    
    /**
     * @param string $filename
     * @param array $config
     * @param array $data
     * @param array $imgUrls
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \Exception
     */
    public function export(string $filename, array $config, array $data, array $imgUrls = []): string
    {
        $imgPath = $this->catchImages($config, $data);
        if (empty($config) || !is_array($config)) {
            throw new CommonException(ErrorCode::UNKNOW);
        }
        $excel = new Spreadsheet();
        //config中的sheet名和data中数组的对应关系
        $sheetIndexMap = [];
        $sheetIndex = 0;
        foreach ($config as $sheetName => $sheetConfig) {
            $excel->setActiveSheetIndex($sheetIndex);
            $sheet = $excel->getActiveSheet();
            //设置sheet名
            $sheet->setTitle($sheetName);
            $sheetIndexMap[$sheetIndex] = $sheetName;
            
            $columnIndex = 'A';
            foreach ($sheetConfig as $columnItem) {
                //设置列名
                $sheet->setCellValue($columnIndex . '1', $columnItem['columnName']);
                //表头样式
                $styleArray = [
                    'borders' => [
                        'outline' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['argb' => '00595959'],
                        ],
                    ],
                    'font' => [
                        'name' => '微软雅黑',
                        'color' => ['argb' => '5E5E5E'],
                        'bold' => true,
                        'size' => 8
                    ],
                    'alignment' => [
                        'horizontal' => $columnItem['align'] ?? Alignment::HORIZONTAL_CENTER,
                        'vertical' => Alignment::VERTICAL_CENTER,
                        'wrapText' => false,
                    ]
                ];
                $sheet->getStyle($columnIndex . '1')->applyFromArray($styleArray);
                
                //设置单元格宽
                $cellLength = $columnItem['width'] ?? 30;
                $sheet->getColumnDimension($columnIndex)->setWidth($cellLength);
                
                $columnIndex++;
            }
            $excel->createSheet();
            $sheetIndex++;
        }
        
        $sheetIndex = 0;
        foreach ($data as $sheetData) {
            $excel->setActiveSheetIndex($sheetIndex);
            $sheet = $excel->getActiveSheet();
            if (isset($sheetIndexMap[$sheetIndex])) {
                //获取数组对应的config
                $configKey = $sheetIndexMap[$sheetIndex];
                $sheetConfig = $config[$configKey];
                $rowIndex = 2;
                $maxColumn = 'A';
                $maxRow = 2;
                foreach ($sheetData as $row) {
                    $columnIndex = 'A';
                    foreach ($sheetConfig as $item) {
                        $text = array_key_exists($item['bindKey'], $row) ? $row[$item['bindKey']] : '未找到的bindKey';
                        if (isset($item['isPicture']) && $item['isPicture']) {
                            $tmpPath = $imgPath[md5($text)] ?? '';
                            if ($tmpPath) {
                                $img = new Drawing();
                                $img->setName('Logo');
                                $img->setDescription('Logo');
                                $img->setOffsetX(10);
                                $img->setOffsetY(10);
                                $img->setPath($tmpPath);
                                $img->setWidth(80);
                                $img->setHeight(80);
                                $img->setCoordinates($columnIndex . $rowIndex);
                                $sheet->getCell($columnIndex . $rowIndex)->getHyperlink()->setUrl($text);
                                $sheet->getRowDimension($rowIndex)->setRowHeight(80);
                                $img->setWorksheet($sheet);
                                $styleArray['alignment']['horizontal'] = 'center';
                            } else {
                                $sheet->setCellValueExplicit($columnIndex . $rowIndex, $text, DataType::TYPE_STRING);
                            }
                        } else {
                            $sheet->setCellValue($columnIndex . $rowIndex, $text);
                            $setWrapText = $item['setWrapText'] ?? false;
                            $styleArray = [
                                'borders' => [
                                    'outline' => [
                                        'borderStyle' => Border::BORDER_THIN,
                                        'color' => ['argb' => '00595959'],
                                    ],
                                ],
                                'font' => [
                                    'name' => '微软雅黑',
                                    'color' => ['argb' => '00595959'],
                                    'size' => 9
                                ],
                                'numberFormat' => [
                                    'formatCode' => $item['format'] ?? 'General',
                                ],
                                'alignment' => [
                                    'horizontal' => $item['align'] ?? Alignment::HORIZONTAL_CENTER,
                                    'vertical' => Alignment::VERTICAL_CENTER,
                                ]
                            ];
                            $setWrapText && $styleArray['alignment']['wrapText'] = true;
                            $sheet->getStyle($columnIndex . $rowIndex)->applyFromArray($styleArray);
                            isset($item['height']) && $sheet->getRowDimension($rowIndex)->setRowHeight($item['height']);
    
                        }
                        $maxColumn = $columnIndex;
                        $columnIndex++;
                    }
                    $maxRow = $rowIndex;
                    $rowIndex++;
                }
                $sheet->setAutoFilter("A1:{$maxColumn}{$maxRow}");
                $sheetIndex++;
            }
        }
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        ob_start();
        $filename = md5($filename . time() . random_int(1000, 9999));
        $dir = storage_path(self::TMP_PATH) . date('Y/m/d/');
        if (!is_dir($dir) && !mkdir($dir, 0700, true) && !is_dir($dir)) {
            throw new RuntimeException(sprintf('Directory "%s" was not created', $dir));
        }
        $filePath = $dir . $filename . '.xlsx';
        $writer = IOFactory::createWriter($excel, 'Xlsx');
        $writer->save($filePath);
        foreach ($imgPath as $path) {
            @unlink($path);
        }
        return $filePath;
    }
    
    protected function catchImages(array $config, array $data): array
    {
        $ret = [];
        $imgUrls = [];
        foreach ($config as $key => $conf) {
            foreach ($conf as $item) {
                if (isset($item['isPicture']) && $item['isPicture']) {
                    $datum = $data[$key];
                    $list = array_column($datum, $item['bindKey']);
                    $imgUrls += array_unique(
                        array_filter($list)
                    );
                }
            }
        }
        foreach (array_chunk($imgUrls, 100) as $imgArr) {
            $requests = [];
            foreach ($imgArr as $v) {
                $requests[md5($v)]['url'] = $v;
            }
            $downloadRet = Download::multiDownloadImg($requests);
            $ret += $downloadRet;
        }
        return $ret;
    }
    
}
