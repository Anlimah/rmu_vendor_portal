<?php

require_once('bootstrap.php');

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Src\Controller\ExposeDataController;

class Broadsheet
{
    private $spreadsheet = null;
    private $writer = null;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
        $this->writer = new Xlsx($this->spreadsheet);
    }

    private function makeSpreadsheetContent()
    {
        $sheet = $this->spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Hello World !');
    }

    private function saveSpreadsheetFile()
    {
        $this->writer->save('hello world.xlsx');
    }

    public function generate()
    {
        $this->makeSpreadsheetContent();
        $this->saveSpreadsheetFile();
    }
}

$broadsheet = new Broadsheet();
$broadsheet->generate();
echo "OKAY";
