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

    private function makeSpreadsheetContent($datasheet)
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');

        $coreExcelColumns = ["B", "C", "D", "E"];
        $elecExcelColumns = ["F", "G", "H", "I"];
        $row = 1;

        foreach ($datasheet as $data) {
            $fullname = $data["pers_details"]["first_name"] . " " . $data["pers_details"]["last_name"];
            if (!empty($data["pers_details"]["middle_name"])) {
                $fullname = $data["pers_details"]["first_name"] . " " . $data["pers_details"]["middle_name"] . " " . $data["pers_details"]["last_name"];
            }

            $coreNextInput = 0;
            $electiveNextInput = 0;

            $nameCell = "A" . $row;
            $sheet->setCellValue($nameCell, $fullname);
            foreach ($data["exam_details"] as $subj) {
                if ($subj["type"] == "core") {
                    $coreCell = $coreExcelColumns[$coreNextInput] . "" . $row;
                    $sheet->setCellValue($coreCell, $subj["grade"]);
                    $coreNextInput += 1;
                }
                if ($subj["type"] == "elective") {
                    $elecCell = $elecExcelColumns[$electiveNextInput] . "" . $row;
                    $sheet->setCellValue($elecCell, $subj["grade"]);
                    $electiveNextInput += 1;
                }
            }
            $row += 1;
        }
    }

    private function saveSpreadsheetFile($filename)
    {
        $this->writer->save($filename . '.xlsx');
    }

    public function generate($prog)
    {
        $datasheet = $this->prepareBSData();
        if (empty($datasheet)) return 0;
        $this->makeSpreadsheetContent($datasheet);
        $filename = strtoupper("List of All Admitted" . ($prog != "all" ? " $prog " : " ") . "Students");

        $this->saveSpreadsheetFile($filename);
    }
}

$broadsheet = new Broadsheet();
$broadsheet->generate("BCS");
echo "OKAY";
