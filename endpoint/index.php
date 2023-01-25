<?php
session_start();
/*
* Designed and programmed by
* @Author: Francis A. Anlimah
*/

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

/*$method = $_SERVER['REQUEST_METHOD'];
$request = explode('/', trim($_SERVER['PATH_INFO'], '/'));
$input = json_decode(file_get_contents('php://input'), true);
die(json_encode($input));*/

require "../bootstrap.php";

use Src\Controller\AdminController;
use Src\Controller\DownloadExcelDataController;
use Src\Controller\UploadExcelDataController;
use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();
$admin = new AdminController();

$data = [];
$errors = [];

// All GET request will be sent here
if ($_SERVER['REQUEST_METHOD'] == "GET") {
    if ($_GET["url"] == "programs") {
        if (isset($_GET["type"])) {
            $t = 0;
            if ($_GET["type"] != "All") {
                $t = (int) $_GET["type"];
            }
            $result = $admin->fetchPrograms($t);
            if (!empty($result)) {
                $data["success"] = true;
                $data["message"] = $result;
            } else {
                $data["success"] = false;
                $data["message"] = "No result found!";
            }
        }
        die(json_encode($data));
    } elseif ($_GET["url"] == "form-price") {
        if (!isset($_GET["form_key"]) || empty($_GET["form_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchFormPrice($_GET["form_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching form price details!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "vendor-form") {
        if (!isset($_GET["vendor_key"]) || empty($_GET["vendor_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchVendor($_GET["vendor_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching form price details!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "prog-form") {
        if (!isset($_GET["prog_key"]) || empty($_GET["prog_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchProgramme($_GET["prog_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching programme informatiion!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }
    //
    elseif ($_GET["url"] == "adp-form") {
        if (!isset($_GET["adp_key"]) || empty($_GET["adp_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchAdmissionPeriod($_GET["adp_key"]);
        if (!$rslt) die(json_encode(array("success" => false, "message" => "Error fetching programme informatiion!")));
        die(json_encode(array("success" => true, "message" => $rslt)));
    }

    // All POST request will be sent here
} elseif ($_SERVER['REQUEST_METHOD'] == "POST") {
    if ($_GET["url"] == "admin-login") {
        if (!isset($_SESSION["_adminLogToken"]) || empty($_SESSION["_adminLogToken"]))
            die(json_encode(array("success" => false, "message" => "Invalid request: 1!")));
        if (!isset($_POST["_vALToken"]) || empty($_POST["_vALToken"]))
            die(json_encode(array("success" => false, "message" => "Invalid request: 2!")));
        if ($_POST["_vALToken"] !== $_SESSION["_adminLogToken"]) {
            die(json_encode(array("success" => false, "message" => "Invalid request: 3!")));
        }
        $username = $expose->validateText($_POST["username"]);
        $password = $expose->validatePassword($_POST["password"]);

        $result = $admin->verifyAdminLogin($username["message"], $password["message"]);

        if (!$result) {
            die(json_encode(array("response" => "error", "message" => "Incorrect application username or password! ")));
        } else {
            $_SESSION['admin'] = $result["id"];
            $_SESSION['role'] = $result["type"];
            $_SESSION['adminLogSuccess'] = true;
            die(json_encode(array("success" => true)));
        }
    }

    //
    elseif ($_GET["url"] == "apps-data") {
        if (!isset($_POST["action"]) || !isset($_POST["form_t"])) die(json_encode(array("success" => false, "message" => "Invalid input!")));
        if (empty($_POST["action"]) || empty($_POST["form_t"])) die(json_encode(array("success" => false, "message" => "Missing request!")));

        $v_action = $expose->validateText($_POST["action"]);
        $v_form_t = $expose->validateNumber($_POST["form_t"]);
        if (!$v_action["success"]) die(json_encode($v_action));
        if (!$v_form_t["success"]) die(json_encode($v_form_t));

        $data = array(
            'action' => $v_action["message"], 'country' => 'All', 'type' => $v_form_t["message"], 'program' => 'All'
        );
        $result = $admin->fetchAppsSummaryData($data);
        if (empty($result)) die(json_encode(array("success" => false, "message" => "Empty result!")));
        die(json_encode(array("success" => true, "message" => $result)));
    }
    //
    elseif ($_GET["url"] == "applicants") {

        if (!isset($_POST["action"]) || !isset($_POST["country"]) || !isset($_POST["type"]) || !isset($_POST["program"])) {
            die(json_encode(array("success" => false, "message" => "Missing input!")));
        }
        if (empty($_POST["action"]) || empty($_POST["country"]) || empty($_POST["type"]) || empty($_POST["program"])) {
            die(json_encode(array("success" => false, "message" => "Missing input!")));
        }

        $result = $admin->fetchAppsSummaryData($_POST);
        if (!empty($result)) {
            $data["success"] = true;
            $data["message"] = $result;
        } else {
            $data["success"] = false;
            $data["message"] = "No result found!";
        }
        die(json_encode($data));
    }
    //
    elseif ($_GET["url"] == "getUnadmittedApps") {

        if (!isset($_POST["cert-type"]) || !isset($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["cert-type"]) || empty($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $result = $admin->fetchAllUnadmittedApplicantsData($_POST["cert-type"], $_POST["prog-type"]);

        if (empty($result)) {
            die(json_encode(array("success" => false, "message" => "No result found!")));
        }
        die(json_encode(array("success" => true, "message" => $result)));
    }
    //
    elseif ($_GET["url"] == "getBroadsheetData") {

        if (!isset($_POST["cert-type"])) die(json_encode(array("success" => false, "message" => "Invalid input field")));
        if (empty($_POST["cert-type"])) die(json_encode(array("success" => false, "message" => "Missing input field")));

        $result = $admin->fetchAllAdmittedApplicantsData($_POST["cert-type"]);

        if (empty($result)) die(json_encode(array("success" => false, "message" => "No result found!")));
        die(json_encode(array("success" => true, "message" => $result)));
    }
    //
    elseif ($_GET["url"] == "admitAll") {
        if (!isset($_POST["cert-type"]) || !isset($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["cert-type"]) || empty($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $result = $admin->admitQualifiedStudents($_POST["cert-type"], $_POST["prog-type"]);

        if (empty($result)) {
            die(json_encode(array("success" => false, "message" => "No result found!")));
        }
        die(json_encode(array("success" => true, "message" => $result)));
    }
    //
    elseif ($_GET["url"] == "downloadBS") {
        if (!isset($_POST["cert-type"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["cert-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $url = "https://office.rmuictonline.com/download-bs.php?a=bs&c=" . $_POST["cert-type"];
        die(json_encode(array("success" => true, "message" => $url)));
    }
    //
    elseif ($_GET["url"] == "downloadAwaiting") {
        $url = "https://office.rmuictonline.com/download-awaiting-ds.php?a=as&c=awaiting";
        die(json_encode(array("success" => true, "message" => $url)));
    }
    //
    elseif ($_GET["url"] == "extra-awaiting-data") {

        if (!isset($_POST["action"]) || empty($_POST["action"])) {
            die(json_encode(array("success" => false, "message" => "Invalid request (1)!")));
        }

        $result;

        switch ($_POST["action"]) {
                // download broadsheet dbs
            case 'dbs':
                $broadsheet = new DownloadExcelDataController($_POST['c']);
                $file = $broadsheet->generateFile();
                $result = $broadsheet->downloadFile($file);
                break;

                // upload awaiting datasheet uad
            case 'uad':

                if (!isset($_FILES["awaiting-ds"]) || empty($_FILES["awaiting-ds"])) {
                    die(json_encode(array("success" => false, "message" => "Invalid request!")));
                }

                if ($_FILES["awaiting-ds"]['error']) {
                    die(json_encode(array("success" => false, "message" => "Failed to upload file!")));
                }

                $startRow = $expose->validateNumber($_POST['startRow']);
                if (!$startRow["success"]) die(json_encode($startRow));

                $endRow = $expose->validateNumber($_POST['endRow']);
                if (!$endRow["success"]) die(json_encode($endRow));

                $excelData = new UploadExcelDataController($_FILES["awaiting-ds"], $_POST['startRow'], $_POST['endRow']);
                $result = $excelData->extractAwaitingApplicantsResults();
                break;
        }

        die(json_encode($result));
    }

    ///
    elseif ($_GET["url"] == "form-price") {
        if (!isset($_POST["form_type"]) || !isset($_POST["form_price"])) {
            die(json_encode(array("success" => false, "message" => "Invalid input field")));
        }
        if (empty($_POST["form_type"]) || empty($_POST["form_price"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $result = [];

        switch ($_POST["action"]) {
            case 'add':
                $rslt = $admin->addFormPrice($_POST["form_type"], $_POST["form_price"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to add price!")));
                }
                $result = array("success" => true, "message" => "Successfully added form price!");
                break;

            case 'update':
                $rslt = $admin->updateFormPrice($_POST["form_type"], $_POST["form_price"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to update price!")));
                }
                $result = array("success" => true, "message" => "Successfully updated form price!");
                break;

            default:
                die(json_encode(array("success" => false, "message" => "Invalid action!")));
                break;
        }

        die(json_encode($result));
    }
    //
    elseif ($_GET["url"] == "vendor-form") {
        if (!isset($_POST["v-name"]) || empty($_POST["v-name"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Vendor Name")));
        }
        if (!isset($_POST["v-tin"]) || empty($_POST["v-tin"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: TIN")));
        }
        if (!isset($_POST["v-email"]) || empty($_POST["v-email"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Email Address")));
        }
        if (!isset($_POST["v-phone"]) || empty($_POST["v-phone"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Phone Number")));
        }

        $result;
        switch ($_POST["v-action"]) {
            case 'add':
                $rslt = $admin->addVendor($_POST["v-name"], $_POST["v-tin"], $_POST["v-email"], $_POST["v-phone"], $_POST["v-address"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to add vendor!")));
                }
                $result = array("success" => true, "message" => "Successfully added vendor!");
                break;

            case 'update':
                $rslt = $admin->updateVendor($_POST["v-id"], $_POST["v-name"], $_POST["v-tin"], $_POST["v-email"], $_POST["v-phone"], $_POST["v-address"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to update vendor information!")));
                }
                $result = array("success" => true, "message" => "Successfully updated vendor information!");
                break;
        }

        die(json_encode($result));
    }
    //
    elseif ($_GET["url"] == "prog-form") {
        if (!isset($_POST["prog-name"]) || empty($_POST["prog-name"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Name")));
        }
        if (!isset($_POST["prog-type"]) || empty($_POST["prog-type"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Type")));
        }
        if (!isset($_POST["prog-wkd"]) || empty($_POST["prog-wkd"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Weekend")));
        }
        if (!isset($_POST["prog-grp"]) || empty($_POST["prog-grp"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Group")));
        }

        $result;
        switch ($_POST["prog-action"]) {
            case 'add':
                $rslt = $admin->addProgramme($_POST["prog-name"], $_POST["prog-type"], $_POST["prog-wkd"], $_POST["prog-grp"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to add vendor!")));
                }
                $result = array("success" => true, "message" => "Successfully added vendor!");
                break;

            case 'update':
                $rslt = $admin->updateProgramme($_POST["prog-id"], $_POST["prog-name"], $_POST["prog-type"], $_POST["prog-wkd"], $_POST["prog-grp"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to update vendor information!")));
                }
                $result = array("success" => true, "message" => "Successfully updated vendor information!");
                break;
        }

        die(json_encode($result));
    }
    //
    elseif ($_GET["url"] == "adp-form") {
        if (!isset($_POST["adp-start"]) || empty($_POST["adp-start"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Name")));
        }
        if (!isset($_POST["adp-end"]) || empty($_POST["adp-end"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Type")));
        }
        if (!isset($_POST["adp-desc"]) || empty($_POST["adp-desc"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Weekend")));
        }

        $result;
        switch ($_POST["adp-action"]) {
            case 'add':
                $rslt = $admin->addAdmissionPeriod($_POST["adp-start"], $_POST["adp-end"], $_POST["adp-desc"]);
                if (!$rslt["success"]) {
                    die(json_encode($rslt));
                }
                break;

            case 'update':
                $rslt = $admin->updateAdmissionPeriod($_POST["adp-id"], $_POST["adp-start"], $_POST["adp-end"], $_POST["adp-desc"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to update admission information!")));
                }
                $result = array("success" => true, "message" => "Successfully updated admission information!");
                break;
        }

        die(json_encode($result));
    }

    // All PUT request will be sent here
} else if ($_SERVER['REQUEST_METHOD'] == "PUT") {
    parse_str(file_get_contents("php://input"), $_PUT);

    if ($_GET["url"] == "adp-form") {
        if (!isset($_PUT["adp_key"]) || empty($_PUT["adp_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $rslt = $admin->closeAdmissionPeriod($_PUT["adp_key"]);

        if (!$rslt) {
            die(json_encode(array("success" => false, "message" => "Failed to delete programme!")));
        }

        die(json_encode(array("success" => true, "message" => "Successfully deleted programme!")));
    }

    // All DELETE request will be sent here
} else if ($_SERVER['REQUEST_METHOD'] == "DELETE") {
    parse_str(file_get_contents("php://input"), $_DELETE);

    if ($_GET["url"] == "form-price") {
        if (!isset($_DELETE["form_key"]) || empty($_DELETE["form_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $rslt = $admin->deleteFormPrice($_DELETE["form_key"]);

        if (!$rslt) {
            die(json_encode(array("success" => false, "message" => "Failed to delete form price!")));
        }

        die(json_encode(array("success" => true, "message" => "Successfully deleted form price!")));
    }

    if ($_GET["url"] == "vendor-form") {
        if (!isset($_DELETE["vendor_key"]) || empty($_DELETE["vendor_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $rslt = $admin->deleteVendor($_DELETE["vendor_key"]);

        if (!$rslt) {
            die(json_encode(array("success" => false, "message" => "Failed to delete form price!")));
        }

        die(json_encode(array("success" => true, "message" => "Successfully deleted form price!")));
    }

    if ($_GET["url"] == "prog-form") {
        if (!isset($_DELETE["prog_key"]) || empty($_DELETE["prog_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $rslt = $admin->deleteProgramme($_DELETE["prog_key"]);

        if (!$rslt) {
            die(json_encode(array("success" => false, "message" => "Failed to delete programme!")));
        }

        die(json_encode(array("success" => true, "message" => "Successfully deleted programme!")));
    }
} else {
    http_response_code(405);
}
