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

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->writer = new Xlsx($this->spreadsheet);
        $this->admin = new AdminController();
    }

    public function prepareBSData()
    {
        $admittedApps = $this->admin->getAllAdmittedAppsPersDetails();
        if (empty($admittedApps)) return 0;

        $data = [];

        foreach ($admittedApps as  $appData) {
            $applicant = [];
            $applicant["pers_details"] = $appData;
            $subjs = $this->admin->getAppCourseSubjects($appData["id"]);
            $applicant["exam_details"] = $subjs;
            array_push($data, $applicant);
        }

        return $data;
    }

    private function makeSpreadsheetContent($datasheet, $title)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', $title);
        $sheet->mergeCells('A1:J1')->setAlign('center');

        $sheet->setCellValue('A2', "Name")->setAlign('center');

        $sheet->setCellValue('B2', "Core Subjects");
        $sheet->mergeCells('B2:E2')->setAlign('center');

        $sheet->setCellValue('F2', "Elective Subjects");
        $sheet->mergeCells('F2:J2')->setAlign('center');

        $sheet->setCellValue('B2', "CORE MATHEMATICS")->setAlign('center');
        $sheet->setCellValue('C2', "ENGLISH LANGUAGE")->setAlign('center');
        $sheet->setCellValue('D2', "INTEGRATED SCIENCE")->setAlign('center');
        $sheet->setCellValue('E2', "SOCIAL STUDIES")->setAlign('center');

        $sheet->setCellValue('F2', "ELECTIVE 1")->setAlign('center');
        $sheet->setCellValue('G2', "ELECTIVE 2")->setAlign('center');
        $sheet->setCellValue('H2', "ELECTIVE 3")->setAlign('center');
        $sheet->setCellValue('I2', "ELECTIVE 4")->setAlign('center');

        $sheet->mergeCells('B2:E2')->setAlign('center');
        $sheet->mergeCells('F2:I2')->setAlign('center');

        $coreExcelColumns = ["B", "C", "D", "E"];
        $elecExcelColumns = ["F", "G", "H", "I"];

        //$format_top->setTextWrap(1);

        $row = 3;

        foreach ($datasheet as $data) {

            // set applicant fullname
            $fullname = $data["pers_details"]["first_name"] . " " . $data["pers_details"]["last_name"];
            if (!empty($data["pers_details"]["middle_name"])) {
                $fullname = $data["pers_details"]["first_name"] . " " . $data["pers_details"]["middle_name"] . " " . $data["pers_details"]["last_name"];
            }

            $coreNextInput = 0;
            $electiveNextInput = 0;

            //set applicant name value
            $appNameCell = "A" . $row;
            $sheet->setCellValue($appNameCell, $fullname);

            foreach ($data["exam_details"] as $subj) {
                // set all core subject value
                if ($subj["type"] == "core") {
                    $coreCell = $coreExcelColumns[$coreNextInput] . "" . $row;
                    $sheet->setCellValue($coreCell, $subj["grade"]);
                    $coreNextInput += 1;
                }

                // set all core subject value
                if ($subj["type"] == "elective") {
                    $elecCell = $elecExcelColumns[$electiveNextInput] . "" . $row;
                    $sheet->setCellValue($elecCell, $subj["grade"]);
                    $electiveNextInput += 1;
                }
            }

            //set program value
            $progNameCell = "J" . $row;
            $sheet->setCellValue($progNameCell, $fullname);

            $row += 1;
        }
    }

    private function saveSpreadsheetFile($filename)
    {
        $file = $filename . '.xlsx';

        if (file_exists($file)) {
            unlink($file);
        }

        $this->writer->save($file);

        $this->spreadsheet->disconnectWorksheets();
        unset($this->spreadsheet);
    }

    public function generate($prog)
    {
        $datasheet = $this->prepareBSData();
        if (empty($datasheet)) return 0;
        $filename = strtoupper("List of All Admitted" . ($prog != "all" ? " $prog " : " ") . "Students");
        $this->makeSpreadsheetContent($datasheet, $filename);
        $this->saveSpreadsheetFile($filename);
    }
}

$broadsheet = new Broadsheet();
$broadsheet->generate("BCS");
echo "OKAY";
