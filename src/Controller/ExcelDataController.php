<?php

namespace Src\Controller;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Src\Controller\AdminController;
use DateTime;

class ExcelDataController
{
    private $spreadsheet = null;
    private $writer = null;
    private $admin = null;
    private $sheet = null;
    private $dataSheet = [];
    private $fileName = null;
    private $sheetTitle = null;
    private $cert_type = null;

    private $fileObj = array();
    private $startRow = null;
    private $endRow = null;
    private $targetPath = null;

    public function __construct(...$args)
    {
        switch (count($args)) {
            case 1:
                $this->__construct_download_bs($args[0]);
                break;
            case 3:
                $this->__construct_upload_awaiting($args[0], $args[1], $args[2]);
                break;
        }
    }

    private function __construct_download_bs($cert_type)
    {
        $this->cert_type = $cert_type;
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
        $admittedApps = $this->admin->getAllAdmitedApplicants($this->cert_type);
        if (empty($admittedApps)) return 0;

        foreach ($admittedApps as  $appData) {
            $applicant = [];
            $applicant["pers_details"] = $appData;
            $subjs = $this->admin->getAppCourseSubjects($appData["id"]);
            $applicant["exam_details"] = $subjs;
            array_push($this->dataSheet, $applicant);
        }
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

        $this->sheet->setCellValue('B3', "CORE MATHEMATICS");
        $this->sheet->setCellValue('C3', "ENGLISH LANGUAGE");
        $this->sheet->setCellValue('D3', "INTEGRATED SCIENCE");
        $this->sheet->setCellValue('E3', "SOCIAL STUDIES");

        $this->sheet->setCellValue('F3', "ELECTIVE 1");
        $this->sheet->setCellValue('G3', "ELECTIVE 2");
        $this->sheet->setCellValue('H3', "ELECTIVE 3");
        $this->sheet->setCellValue('I3', "ELECTIVE 4");

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

        $this->sheet->getStyle('A1:J3')->getAlignment()->setHorizontal('center');
        $this->sheet->getStyle('A2:A3')->getAlignment()->setVertical('center');
        $this->sheet->getStyle('J2:J3')->getAlignment()->setVertical('center');
    }

    private function makeSpreadsheetContent($datasheet)
    {
        $coreExcelColumns = ["B", "C", "D", "E"];
        $elecExcelColumns = ["F", "G", "H", "I"];

        //$format_top->setTextWrap(1);

        $row = 4;

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
            $this->sheet->setCellValue($appNameCell, $fullname);

            foreach ($data["exam_details"] as $subj) {
                // set all core subject value
                if ($subj["type"] == "core") {
                    $coreCell = $coreExcelColumns[$coreNextInput] . "" . $row;
                    $this->sheet->setCellValue($coreCell, $subj["grade"]);
                    $coreNextInput += 1;
                }

                // set all core subject value
                if ($subj["type"] == "elective") {
                    $elecCell = $elecExcelColumns[$electiveNextInput] . "" . $row;
                    $this->sheet->setCellValue($elecCell, $subj["grade"]);
                    $electiveNextInput += 1;
                }
            }

            //set program value
            $progNameCell = "J" . $row;
            $this->sheet->setCellValue($progNameCell, $data["pers_details"]["programme"]);

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

    public function createFileName($cert_type)
    {
        $dateData = $this->admin->getAcademicPeriod();
        $this->fileName = strtoupper("List of Admitted" . ($cert_type != "all" ? " $cert_type " : " ") . "Applicants");
        $academicIntake = $dateData[0]["start_year"] . " - " . $dateData[0]["end_year"] . " " . $dateData[0]["info"];
        $this->sheetTitle = $this->fileName . "(" . strtoupper($academicIntake) . ")";
    }

    public function generateFile()
    {
        $this->prepareBSData();
        if (!empty($this->dataSheet)) {
            $this->createFileName($this->cert_type);
            $this->formatSpreadsheet($this->sheetTitle);
            $this->makeSpreadsheetContent($this->dataSheet);
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

    /**
     * Uploading awaiting students data
     */

    private function __construct_upload_awaiting($fileObj, $startRow, $endRow)
    {
        $this->fileObj = $fileObj;
        $this->startRow = (int) $startRow;
        $this->endRow = (int) $endRow;
        $this->admin = new AdminController();
    }

    public function extractAwaitingData()
    {

        $allowedFileType = [
            'application/vnd.ms-excel',
            'text/xls',
            'text/xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
        ];

        if (!in_array($this->fileObj["type"], $allowedFileType)) {
            return array("success" => false, "message" => "Invalid file type. Please choose an excel file!");
        }

        if ($this->fileObj['error'] == UPLOAD_ERR_OK) {

            // Create a unique file name
            $name = time() . '-' . 'awaiting.xlsx';

            // Create the full path to the file
            $this->targetPath = UPLOAD_DIR . "/awaiting/" . $name;

            // Delete file if exsists
            if (file_exists($this->targetPath)) {
                unlink($this->targetPath);
            }

            // Move the file to the target directory
            if (move_uploaded_file($this->fileObj['tmp_name'], $this->targetPath)) {
                //return array("success" => false, "message" => "File upload successful!");
                return $this->getExcelDataIntoDB();
            }
        }
    }

    public function saveSubjectAndGrades($subjects = array(), $aca_id)
    {
        if (!empty($subjects)) {
            $sql = "INSERT INTO `high_school_results` (`type`, `subject`, `grade`, `acad_back_id`) VALUES (:t, :s, :g, :ai)";

            // add core subjects
            for ($i = 0; $i < count($subjects["core"]); $i++) {
                $params = array(":t" => "core", ":s" => $subjects["core"][$i]["subject"], ":g" => $subjects["core"][$i]["grade"], ":ai" =>  $aca_id);
                $this->admin->inputData($sql, $params);
            }

            // add elective subjects
            for ($i = 0; $i < count($subjects["elective"]); $i++) {
                $params = array(":t" => "elective", ":s" => $subjects["elective"][$i]["subject"], ":g" => $subjects["elective"][$i]["grade"], ":ai" =>  $aca_id);
                $this->admin->inputData($sql, $params);
            }

            return 1;
        }
        return 0;
    }

    public function getExcelDataIntoDB()
    {
        $Reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $spreadSheet = $Reader->load($this->targetPath);
        $excelSheet = $spreadSheet->getActiveSheet();
        $spreadSheetArray = $excelSheet->toArray();

        if ($this->endRow == 0) $this->endRow = count($spreadSheetArray);
        if ($this->startRow > 1) $this->startRow -= 1;

        $count = 0;

        for ($i = $this->startRow; $i <= $this->endRow - 1; $i++) {
            $admisNum = $spreadSheetArray[$i][0];
            $indexNum = $spreadSheetArray[$i][1];
            $examMonth = $spreadSheetArray[$i][2];
            $examYear = $spreadSheetArray[$i][3];

            // Get all the courses

            $endRowData = count($spreadSheetArray[$i]);
            $examResults = array();

            for ($j = 6; $j < $endRowData; $j += 2) {
                if ($spreadSheetArray[$i][$j] == "") break;
                array_push($examResults, array(
                    "subject" => $spreadSheetArray[$i][$j],
                    "grade" => $spreadSheetArray[$i][($j + 1)]
                ));

                if (preg_match("/^english lang$/i", $spreadSheetArray[$i][$j])) {
                    echo json_encode(array("" => $spreadSheetArray[$i][$j]));
                }

                if (preg_match("/^mathematics(core)$/i", $spreadSheetArray[$i][$j])) {
                    echo json_encode(array("" => $spreadSheetArray[$i][$j]));
                }

                if (preg_match("/^social studies$/i", $spreadSheetArray[$i][$j])) {
                    echo  json_encode(array("" => $spreadSheetArray[$i][$j]));
                }

                if (preg_match("/^integrated science$/i", $spreadSheetArray[$i][$j])) {
                    echo  json_encode(array("" => $spreadSheetArray[$i][$j]));
                }
            }
            return $examResults;
        }
        //echo "<script>alert('Successfully transfered " . $count . " excel data into DB')</script>";
        //return 1;
    }
}
