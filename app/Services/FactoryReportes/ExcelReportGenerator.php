<?php

namespace App\Services\FactoryReportes;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;


class ExcelReportGenerator implements ReportGenerator
{
    private static $SpreadSheet;
    private static $j;
    private static $excel;
    private $filepath;
    private $path;
    private $columns;
    private $file;
    private $title;

    public function initializa()
    {
        $this->filepath = ($this->path) ? "{$this->path}/{$this->file}.xls" : storage_path() . "/temp/{$this->file}.xls";
        self::$SpreadSheet = new Spreadsheet();
        self::$excel = self::$SpreadSheet->getActiveSheet();

        self::$j = 0;
        if ($this->title) $this->addTitle();
        if ($this->columns) $this->addColumns($this->columns, 11);
    }

    public function addTitle($title = '', $position = 0)
    {
        $columnStyle = self::$SpreadSheet->getStyle(self::$j, 0)->getFont()->setName('Verdana');
        $columnStyle->setSize(13);
        $columnStyle->setBold(true);
        $columnStyle->setItalic(false);
        $columnStyle->setColor('000000');

        $title = ($title != '') ? $title : $this->title;
        $position = ($position) ? $position : (count($this->columns) - 1);

        self::$excel->setMerge(0, 0, 0, $position);
        self::$excel->write(self::$j, 0, $title, $columnStyle);
        self::$j++;
    }

    /**
     * generateReport function
     * Generar reporte en Excel con las condiciones aplicadas a los trabajadores
     * @param [type] $trabajadores
     * @return void
     */
    public function generateReport($title,  $file, $columns)
    {
        $this->file = $file;
        $this->columns = $columns;
        $this->title = $title;
        $this->initializa();
    }

    /**
     * addLine function
     * @return void
     */
    public function addLine($data, $fsize = 10, $cborder = 1)
    {
        $columnStyle = self::$SpreadSheet->getStyle(self::$j, 0)->getFont()->setName('Verdana');
        $columnStyle->setSize($fsize);
        $columnStyle->setBold(false);
        $columnStyle->setItalic(false);
        $columnStyle->setColor('000000');

        $i = 0;
        foreach ($data as $val) self::$excel->write(self::$j, $i++, $val, $columnStyle);
        self::$j++;
    }

    public function addHeader($subtitle, $col, $fsize = 9, $cborder = 1)
    {
        $columnStyle = self::$SpreadSheet->getStyle(self::$j, 0)->getFont()->setName('Verdana');
        $columnStyle->setSize($fsize);
        $columnStyle->setBold(false);
        $columnStyle->setItalic(false);
        $columnStyle->setColor('000000');

        self::$excel->setMerge(self::$j, 0, 0, $col);
        self::$excel->write(self::$j, 0, $subtitle, $columnStyle);
        self::$j++;
    }

    public function addPage($name, $titlePage, $fsize = 11, $col = 1)
    {
        $titleStyle = self::$SpreadSheet->getStyle(self::$j, 0)->getFont()->setName('Verdana');
        $titleStyle->setSize($fsize);
        $titleStyle->setBold(false);
        $titleStyle->setItalic(false);
        $titleStyle->setColor('000000');

        self::$excel = self::$SpreadSheet->addWorksheet($name);
        self::$excel->setMerge(0, 0, 0, $col - 1);
        self::$excel->write(0, 0, $titlePage, $titleStyle);
        self::$j = 1;
    }

    public function addColumns($columns, $fsize = 11)
    {
        $this->columns = $columns;
        $columnTitle = self::$SpreadSheet->getStyle(self::$j, 0)->getFont()->setName('Verdana');
        $columnTitle->setSize($fsize);
        $columnTitle->setBold(false);
        $columnTitle->setItalic(false);
        $columnTitle->setColor('000000');
        $columnTitle->setAlignment('center');

        $i = 0;
        foreach ($columns as $ai => $column) {
            $value = ucfirst($column[0]);
            self::$excel->setColumn($column[1], $column[2], $column[3]);
            self::$excel->write(self::$j, $i++, $value, $columnTitle);
        }
        self::$j++;
    }

    public function outFile()
    {
        $writer = new Xlsx(self::$SpreadSheet);
        $writer->save($this->filepath);
        self::$SpreadSheet->close();
        self::$excel = null;
        self::$j = 0;
        return $this->filepath;
    }

    public function useExcel()
    {
        return self::$excel;
    }

    public function getJ()
    {
        return self::$j;
    }

    public function setJ($j)
    {
        self::$j = $j;
    }

    public function setPath($path)
    {
        $this->path = $path;
    }
}
