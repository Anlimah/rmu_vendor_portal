<?php

namespace Src\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Src\Controller\AdminController;

class DownloadAllExcelDataController
{
    private $admin = null;

    private $spreadsheet = null;
    private $writer = null;
    private $sheet = null;
    private $dataSheet = [];
    private $fileName = null;
    private $sheetTitle = null;
    private $status = null;

    public function __construct($status)
    {
        $this->status = $status;
        $this->spreadsheet = new Spreadsheet();
        $this->sheet = $this->spreadsheet->getActiveSheet();
        $this->writer = new Xlsx($this->spreadsheet);
        $this->admin = new AdminController();
    }

    /**
     * Download broadsheet
     */

    public function prepareBSData()
    {
        return $this->admin->exportAdmissionData($this->status);
    }

    public function formatSpreadsheet($title)
    {
        $this->sheet->setCellValue('A1', $title);
        $this->sheet->mergeCells('A1:J1');

        $this->sheet->setCellValue('A2', "NAME");
        $this->sheet->mergeCells('A2:A3');

        $this->sheet->setCellValue('J2', "PROGRAMME");
        $this->sheet->mergeCells('J2:J3');

        $this->sheet->setCellValue('B2', "CORE SUBJECTS");
        $this->sheet->mergeCells('B2:E2');

        $this->sheet->setCellValue('F2', "ELECTIVE SUBJECTS");
        $this->sheet->mergeCells('F2:I2');

        $this->sheet->getColumnDimension('A')->setAutoSize(true);
        $this->sheet->getColumnDimension('B')->setAutoSize(true);
        $this->sheet->getColumnDimension('C')->setAutoSize(true);
        $this->sheet->getColumnDimension('D')->setAutoSize(true);
        $this->sheet->getColumnDimension('E')->setAutoSize(true);
        $this->sheet->getColumnDimension('F')->setAutoSize(true);
        $this->sheet->getColumnDimension('G')->setAutoSize(true);
        $this->sheet->getColumnDimension('H')->setAutoSize(true);
        $this->sheet->getColumnDimension('I')->setAutoSize(true);
        $this->sheet->getColumnDimension('J')->setAutoSize(true);
    }

    public function makeSpreadsheetContent($datasheet)
    {
        $columnRange = range("B", "BT"); //["B"..."BT"];
        return $columnRange;
        $row = 2;

        foreach ($datasheet as $data) {

            $dataIndex = 0;

            foreach ($data as $d) {
                $cell = $columnRange[$dataIndex] . "" . $row;
                $this->sheet->setCellValue($cell, $d);
                $dataIndex += 1;
            }

            $row += 1;
        }
    }

    public function saveSpreadsheetFile($filename)
    {
        $file = $filename . '.xlsx';

        if (file_exists($file)) {
            unlink($file);
        }

        $this->writer->save($file);

        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function createFileName($status)
    {
        $dateData = $this->admin->getAcademicPeriod();
        $this->fileName = strtoupper("List of " . ($status != "all" ? " $status " : " ") . "Applicants");
        $academicIntake = $dateData[0]["start_year"] . " - " . $dateData[0]["end_year"] . " " . $dateData[0]["info"];
        $this->sheetTitle = $this->fileName . "(" . strtoupper($academicIntake) . ")";
    }

    public function generateFile()
    {
        $dataSheet = $this->prepareBSData();
        if (!empty($dataSheet)) {
            $this->createFileName($this->status);
            //$this->formatSpreadsheet($this->sheetTitle);
            $this->makeSpreadsheetContent($dataSheet);
            $this->saveSpreadsheetFile($this->fileName);
            return $this->fileName;
        }
    }

    public function downloadFile($file)
    {
        $file_url = './' . $file . ".xlsx";
        header('Content-Type:application/octet-stream');
        header("Content-Transfer-Encoding:utf-8");
        header("Content-disposition:attachment;filename=\"" . basename($file_url) . "\"");
        if (readfile($file_url)) return true;
        return array("success" => false, "message" => "Download failed!");
    }
}
