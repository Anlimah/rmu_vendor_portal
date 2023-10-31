<?php

require_once('bootstrap.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Src\Controller\AdminController;

class Broadsheet
{
    private $spreadsheet = null;
    private $writer = null;
    private $admin = null;
    private $sheet = null;
    private $dataSheet = [];
    public $fileNames = [];
    private $admin_period = null;

    public function __construct($admin_period)
    {
        $this->admin = new AdminController();
        $this->admin_period = $admin_period;
    }

    public function prepareBSData()
    {
        $awaitingApps = $this->admin->fetchAllAwaitingApplicationsBS($this->admin_period);
        $awaitingAppsGrp = $this->admin->fetchAllAwaitingApplicationsBSGrouped($this->admin_period);
        if (empty($awaitingApps) || empty($awaitingAppsGrp)) return 0;
        //if (empty($this->admin->saveDownloadedAwaitingResults($awaitingApps))) return 0;
        $this->dataSheet = array("awaitingApps" => $awaitingApps, "awaitingAppsGrp" => $awaitingAppsGrp);
        return 1;
    }

    public function formatSpreadsheet()
    {
        $this->sheet->setCellValue('A1', "AdmissionNumber");
        $this->sheet->setCellValue('B1', "IndexNumber");
        $this->sheet->setCellValue('C1', "ExamMonth");
        $this->sheet->setCellValue('D1', "ExamYear");

        $this->sheet->getColumnDimension('A')->setAutoSize(true);
        $this->sheet->getColumnDimension('B')->setAutoSize(true);
        $this->sheet->getColumnDimension('C')->setAutoSize(true);
        $this->sheet->getColumnDimension('D')->setAutoSize(true);

        $this->sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');
    }

    private function makeSpreadsheetContent($datasheet, $dataPos)
    {
        $row = 2;
        foreach ($datasheet as $data) {
            $this->sheet->setCellValue("A" . $row, $data["AdmissionNumber"]);
            $this->sheet->setCellValue("B" . $row, $data["IndexNumber"]);
            $this->sheet->setCellValue("C" . $row, $data["ExamMonth"]);
            $this->sheet->setCellValue("D" . $row, $data["ExamYear"]);
            $row += 1;
        }
    }

    private function saveSpreadsheetFile($filename)
    {
        $file = "./awaiting_results/" . $filename . '.xlsx';
        if (file_exists($file)) unlink($file);
        $this->writer->save($file);
        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function createFileName($program)
    {
        $dateData = $this->admin->getAcademicPeriod($this->admin_period);
        $fileName = "{$program} - Awaiting Results Applicants (" . $dateData[0]["start_year"] . " - " . $dateData[0]["end_year"] . ")";
        return $fileName;
    }

    public function generateFile(): mixed
    {
        if ($this->prepareBSData()) {
            $count = 0;

            foreach ($this->dataSheet["awaitingAppsGrp"] as $grp) {
                echo "Count: " . ($count + 1) . $grp["Program"] . "<br>";
                $fileName = $this->createFileName($grp["Program"]);
                array_push($this->fileNames, $fileName);

                /*$this->spreadsheet = new Spreadsheet();
                $this->sheet = $this->spreadsheet->getActiveSheet();
                $this->writer = new Xlsx($this->spreadsheet);

                //$this->formatSpreadsheet();
                $this->sheet->setCellValue('A1', "AdmissionNumber");
                $this->sheet->setCellValue('B1', "IndexNumber");
                $this->sheet->setCellValue('C1', "ExamMonth");
                $this->sheet->setCellValue('D1', "ExamYear");
                $this->sheet->getColumnDimension('A')->setAutoSize(true);
                $this->sheet->getColumnDimension('B')->setAutoSize(true);
                $this->sheet->getColumnDimension('C')->setAutoSize(true);
                $this->sheet->getColumnDimension('D')->setAutoSize(true);
                $this->sheet->getStyle('A1:D1')->getAlignment()->setHorizontal('center');*/

                /*$row = 2;

                foreach ($this->dataSheet["awaitingApps"] as $appData) {
                    echo "Row: " . (($row * 0) + 1) . $appData["AdmissionNumber"] . "<br>";
                    if ($grp["Program"] == $appData["Program"]) {
                        //$this->makeSpreadsheetContent($appData);
                        $this->sheet->setCellValue("A" . $row, $appData["AdmissionNumber"]);
                        $this->sheet->setCellValue("B" . $row, $appData["IndexNumber"]);
                        $this->sheet->setCellValue("C" . $row, $appData["ExamMonth"]);
                        $this->sheet->setCellValue("D" . $row, $appData["ExamYear"]);
                        $row += 1;
                    }
                }*/

                // Save spreadsheet file
                //$this->saveSpreadsheetFile($fileName);
                $file = "./awaiting_results/" . $fileName . '.xlsx';
                echo "File name: " . $file . "<br>";
                if (file_exists($file)) unlink($file);
                echo "Continued <br>";
                //$this->writer->save($file);
                echo "save <br>";
                //$this->spreadsheet->disconnectWorksheets();
                echo "disconnectWorksheets <br>";
                //unset($this->spreadsheet);
                echo "unset <br>";

                $count += 1;
            }
            return $count;
        }
        return 0;
    }

    public function downloadFile($file)
    {
        $file_url = './awaiting_results/' . $file . ".xlsx";
        header('Content-Type:application/octet-stream');
        header("Content-Transfer-Encoding:utf-8");
        header("Content-disposition:attachment;filename=\"" . basename($file_url) . "\"");
        readfile($file_url);
    }
}

$broadsheet = new Broadsheet($_GET["ap"]);
$result = $broadsheet->generateFile();
/*if ($result) {
    foreach ($broadsheet->fileNames as $file) {
        $broadsheet->downloadFile($file);
    }
}*/
