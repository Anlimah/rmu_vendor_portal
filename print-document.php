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

    private function makeSpreadsheetContent()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
    }

    private function saveSpreadsheetFile($filename)
    {
        $this->writer->save($filename . '.xlsx');
    }

    public function generate($prog)
    {
        $data = $this->prepareBSData();
        var_dump(json_encode($data));
        /*$this->makeSpreadsheetContent();
        $filename = strtoupper("List of All Admitted" . ($prog != "all" ? " $prog " : " ") . "Students");

        $this->saveSpreadsheetFile($filename);*/
    }
}

$broadsheet = new Broadsheet();
$broadsheet->generate("");
echo "OKAY";
