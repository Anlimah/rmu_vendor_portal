<?php
if (isset($_POST["extractFile"])) {
    $allowedFileType = [
        'application/vnd.ms-excel',
        'text/xls',
        'text/xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'
    ];

    $start = 1;
    $end = 0;
    if (!empty($_POST["start-row"]) && $_POST["start-row"] > 1) {
        $start = $_POST["start-row"];
    }
    if (!empty($_POST["end-row"]) && $_POST["end-row"] > 0) {
        $end = $_POST["end-row"];
    }

    if ($end > 0 && $start > $end) {
        echo '<script>alert("End row number can not be smaller than start row number!");</script>';
    } else {

        if (in_array($_FILES["file"]["type"], $allowedFileType)) {
            $targetPath = 'uploads/' . $_FILES['file']['name'];
            if (file_exists($targetPath)) {
                unlink($targetPath);
            }
            if (move_uploaded_file($_FILES['file']['tmp_name'], $targetPath)) {
                echo "<script>alert('The file " . $_FILES["file"]["name"] . " has been uploaded.')</script>";
                if ($admin->getExcelDataIntoDB($targetPath, $start, $end) < 1) {
                    echo '<script>alert("End row number can not be smaller than start row number!");</script>';
                }
            } else {
                echo "<script>alert('The file upload failed.')</script>";
            }
        }
    }
}
