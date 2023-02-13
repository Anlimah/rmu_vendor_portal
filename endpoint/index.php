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
    //
    elseif ($_GET["url"] == "user-form") {
        if (!isset($_GET["user_key"]) || empty($_GET["user_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }
        $rslt = $admin->fetchSystemUser($_GET["user_key"]);
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

        $result = $admin->verifyAdminLogin($username, $password);

        if (!$result) {
            die(json_encode(array("success" => false, "message" => "Incorrect application username or password! ")));
            $_SESSION['adminLogSuccess'] = false;
        } else {
            $_SESSION['user'] = $result[0]["id"];
            $_SESSION['role'] = $result[0]["role"];
            $_SESSION['adminLogSuccess'] = true;
            die(json_encode(array("success" => true,  "message" => strtolower($result[0]["role"]))));
        }
    }

    // Send OTP verification code to vendor to verify vendor
    elseif ($_GET["url"] == "send-vendor-vc") {
        if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {

            //redirect if user already verifed and session not closed
            if (isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"])) {
                $data["success"] = true;
                $data["message"] = "sell.php";
            } else {
                $vendorPhone = $expose->getVendorPhoneByUserID($_SESSION["user"]);
                if (!empty($vendorPhone)) {
                    $response = $expose->sendOTP($vendorPhone[0]["phone_number"]);

                    if (isset($response["otp_code"])) {
                        $_SESSION['sms_code'] = $response["otp_code"];
                        $_SESSION['verifySMSCode'] = true;
                        $data["success"] = true;
                        $data["message"] = "verify.php?verify=vendor";
                    } else {
                        $data["success"] = false;
                        $data["message"] = $response["statusDescription"];
                    }
                } else {
                    $data["success"] = false;
                    $data["message"] = "No phone number entry found for this user!";
                }
            }
        }
        die(json_encode($data));
    }

    // Resend verification code
    elseif ($_GET["url"] == "resend-code") {
        if (!isset($_POST["resend_code"])) die(json_encode(array("success" => false, "message" => "Invalid request!")));
        if (empty($_POST["resend_code"])) die(json_encode(array("success" => false, "message" => "Missing input!")));

        $code_type = $expose->validateText($_POST["resend_code"]);
        switch ($code_type) {
            case 'sms':
                // For vendor resend otp code
                if (isset($_SESSION["_verifySMSToken"]) && !empty($_SESSION["_verifySMSToken"]) && isset($_POST["_vSMSToken"]) && !empty($_POST["_vSMSToken"]) && $_POST["_vSMSToken"] == $_SESSION["_verifySMSToken"]) {

                    $vendorPhone = $expose->getVendorPhoneByUserID($_SESSION["user"]);

                    if (!empty($vendorPhone)) {
                        $response = $expose->sendOTP($vendorPhone[0]["phone_number"]);

                        if (isset($response["otp_code"])) {
                            $_SESSION['sms_code'] = $response["otp_code"];
                            $_SESSION['verifySMSCode'] = true;
                            $data["success"] = true;
                            $data["message"] = "Verification code sent!";
                        } else {
                            $data["success"] = false;
                            $data["message"] = $response["statusDescription"];
                        }
                    } else {
                        $data["success"] = false;
                        $data["message"] = "No phone number entry found for this user!";
                    }
                }

                // for user/applicant/online resend otp code
                else if (isset($_SESSION["_step5Token"]) && !empty($_SESSION["_step5Token"]) && isset($_POST["_v5Token"]) && !empty($_POST["_v5Token"]) && $_POST["_v5Token"] == $_SESSION["_step5Token"]) {

                    $to = $_SESSION["step4"]["country_code"] . $_SESSION["step4"]["phone_number"];
                    $response = $expose->sendOTP($to);

                    if (isset($response["otp_code"])) {
                        $_SESSION['sms_code'] = $response["otp_code"];
                        $data["success"] = true;
                        $data["message"] = "Verification code resent!";
                    } else {
                        $data["success"] = false;
                        $data["message"] = $response["statusDescription"];
                    }
                } else {
                    die(json_encode(array("success" => false, "message" => "Invalid OTP SMS request!")));
                }
                break;
        }
        die(json_encode($data));
    }

    //After a successfull login, verify vendor mobile phone before redirection to home page
    elseif ($_GET["url"] == "verifyVendor") {
        if (isset($_SESSION["_verifySMSToken"]) && !empty($_SESSION["_verifySMSToken"]) && isset($_POST["_vSMSToken"]) && !empty($_POST["_vSMSToken"]) && $_POST["_vSMSToken"] == $_SESSION["_verifySMSToken"]) {
            if (isset($_POST["code"]) && !empty($_POST["code"])) {

                $otp_code = $expose->validatePhone($_POST["code"]);

                if ($otp_code == $_SESSION['sms_code']) {
                    $_SESSION["admin_period"] = $expose->getCurrentAdmissionPeriodID();
                    $_SESSION["vendor_id"] = $expose->getVendorPhoneByUserID($_SESSION["user"])[0]["id"];
                    $_SESSION["SMSLogin"] = true;
                    $_SESSION["loginSuccess"] = true;
                    $data["success"] = true;
                    $data["message"] = "sell.php";
                } else {
                    $data["success"] = false;
                    $data["message"] = "Entry did not match OTP code sent!!";
                }
            } else {
                $data["success"] = false;
                $data["message"] = "Code entries are needed!";
            }
        } else {
            $data["success"] = false;
            $data["message"] = "Invalid request!";
        }
        die(json_encode($data));
    }

    // Get details on form
    elseif ($_GET["url"] == "formInfo") {
        if (isset($_POST["form_type"]) && !empty($_POST["form_type"])) {
            $form_type = $expose->validateInput($_POST["form_type"]);
            $result = $expose->getFormPriceAndFormName($form_type, $expose->getCurrentAdmissionPeriodID());
            if (!empty($result)) {
                $data["success"] = true;
                $data["message"] = $result;
            } else {
                $data["success"] = false;
                $data["message"] = "Amount not set";
            }
        } else {
            $data["success"] = false;
            $data["message"] = "Amount not set";
        }
        die(json_encode($data));
    }

    //Vendor endpoint
    elseif ($_GET["url"] == "sellAction") {
        if (isset($_SESSION["_vendor1Token"]) && !empty($_SESSION["_vendor1Token"]) && isset($_POST["_v1Token"]) && !empty($_POST["_v1Token"]) && $_POST["_v1Token"] == $_SESSION["_vendor1Token"]) {

            if (!isset($_POST["form_type"]) || empty($_POST["form_type"]));
            if (!isset($_POST["first_name"]) || empty($_POST["first_name"]));
            if (!isset($_POST["last_name"]) || empty($_POST["last_name"]));
            if (isset($_POST["country"]) || empty($_POST["country"]));
            if (isset($_POST["phone_number"]) || empty($_POST["phone_number"]));

            $first_name = $expose->validateText($_POST["first_name"]);
            $last_name = $expose->validateText($_POST["last_name"]);
            $phone_number = $expose->validatePhone($_POST["phone_number"]);
            $country = $expose->validateCountryCode($_POST["country"]);

            $charPos = strpos($country, ")");
            $country_name = substr($country, ($charPos + 2));
            $country_code = substr($country, 1, ($charPos - 1));

            $form_type = $expose->validateInput($_POST["form_type"]);
            $amount = $expose->getFormPrice($form_type, $_SESSION["admin_period"]);

            if (!empty($amount)) {
                $_SESSION["vendorData"] = array(
                    "first_name" => $first_name,
                    "last_name" => $last_name,
                    "country_name" => $country_name,
                    "country_code" => $country_code,
                    "phone_number" => $phone_number,
                    "email_address" => "",
                    "form_type" => $form_type,
                    "pay_method" => "CASH",
                    "amount" => $amount[0]["amount"],
                    "vendor_id" => $_SESSION["vendor_id"],
                    "admin_period" => $_SESSION["admin_period"]
                );

                if (!empty($_SESSION["vendorData"])) {
                    $to = $_SESSION["vendorData"]["country_code"] . $_SESSION["vendorData"]["phone_number"];
                    $response = $expose->sendOTP($to);

                    if (isset($response["otp_code"])) {
                        $_SESSION['sms_code'] = $response["otp_code"];
                        $_SESSION['verifySMSCode'] = true;
                        $data["success"] = true;
                        $data["message"] = "verify.php?verify=customer";
                    } else {
                        $_SESSION['verifySMSCode'] = false;
                        $data["success"] = false;
                        $data["message"] = $response["statusDescription"];
                    }
                } else {
                    $data["success"] = false;
                    $data["message"] = "Failed in preparing data submitted!";
                }
            } else {
                $data["success"] = false;
                $data["message"] = "Unset data values!";
            }
        } else {
            $data["success"] = false;
            $data["message"] = "Invalid request!1";
        }
        die(json_encode($data));
    }

    //Verify customer phone number before sending Application login details
    elseif ($_GET["url"] == "verifyCustomer") {
        if (isset($_SESSION["_verifySMSToken"]) && !empty($_SESSION["_verifySMSToken"]) && isset($_POST["_vSMSToken"]) && !empty($_POST["_vSMSToken"]) && $_POST["_vSMSToken"] == $_SESSION["_verifySMSToken"]) {

            if (isset($_POST["code"]) && !empty($_POST["code"])) {
                $otp_code = $expose->validatePhone($_POST["code"]);

                if ($otp_code == $_SESSION['sms_code']) {
                    if (isset($_SESSION["vendorData"]) && !empty($_SESSION["vendorData"])) {
                        if ($expose->vendorExist($_SESSION["vendorData"]["vendor_id"])) {
                            $data = $admin->processVendorPay($_SESSION["vendorData"]);
                        } else {
                            $data["success"] = false;
                            $data["message"] = "Process can only be performed by a vendor!";
                        }
                    } else {
                        $data["success"] = false;
                        $data["message"] = "Empty data payload!";
                    }
                } else {
                    $data["success"] = false;
                    $data["message"] = "Entry did not match OTP code sent!";
                }
            } else {
                $data["success"] = false;
                $data["message"] = "Code entries are needed!";
            }
        } else {
            $data["success"] = false;
            $data["message"] = "Invalid request!";
        }
        die(json_encode($data));
    }

    //
    elseif ($_GET["url"] == "apps-data") {
        if (!isset($_POST["action"]) || !isset($_POST["form_t"])) die(json_encode(array("success" => false, "message" => "Invalid input!")));
        if (empty($_POST["action"]) || empty($_POST["form_t"])) die(json_encode(array("success" => false, "message" => "Missing request!")));

        $v_action = $expose->validateText($_POST["action"]);
        $v_form_t = $expose->validateNumber($_POST["form_t"]);
        $data = array('action' => $v_action, 'country' => 'All', 'type' => $v_form_t, 'program' => 'All');
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
                $endRow = $expose->validateNumber($_POST['endRow']);

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
                $rslt = $admin->updateAdmissionPeriod($_POST["adp-id"], $_POST["adp-start"], $_POST["adp-desc"]);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to update admission information!")));
                }
                $result = array("success" => true, "message" => "Successfully updated admission information!");
                break;
        }

        die(json_encode($result));
    }
    //
    elseif ($_GET["url"] == "user-form") {
        if (!isset($_POST["user-fname"]) || empty($_POST["user-fname"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: First name")));
        }
        if (!isset($_POST["user-lname"]) || empty($_POST["user-lname"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Last name")));
        }
        if (!isset($_POST["user-email"]) || empty($_POST["user-email"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Email")));
        }
        if (!isset($_POST["user-role"]) || empty($_POST["user-role"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field: Role")));
        }

        if ($_POST["user-role"] == "Vendors") {
            if (!isset($_POST["vendor-tin"]) || empty($_POST["vendor-tin"])) {
                die(json_encode(array("success" => false, "message" => "Missing input field: Ghana Card")));
            }
            if (!isset($_POST["vendor-phone"]) || empty($_POST["vendor-phone"])) {
                die(json_encode(array("success" => false, "message" => "Missing input field: Phone Number")));
            }
            if (!isset($_POST["vendor-company"]) || empty($_POST["vendor-company"])) {
                die(json_encode(array("success" => false, "message" => "Missing input field: Address")));
            }
            if (!isset($_POST["vendor-address"]) || empty($_POST["vendor-address"])) {
                die(json_encode(array("success" => false, "message" => "Missing input field: Address")));
            }
        }

        $user_data = array(
            "first_name" => $_POST["user-fname"], "last_name" => $_POST["user-lname"],
            "user_name" => $_POST["user-email"], "user_role" => $_POST["user-role"],
            "vendor_company" => $_POST["vendor-company"], "vendor_tin" => $_POST["vendor-tin"],
            "vendor_phone" => $_POST["vendor-phone"], "vendor_address" => $_POST["vendor-address"]
        );

        $privileges = array("select" => 1, "insert" => 0, "update" => 0, "delete" => 0);
        if (isset($_POST["privileges"]) && !empty($_POST["privileges"])) {
            foreach ($_POST["privileges"] as $privilege) {
                if ($privilege == "insert") $privileges["insert"] = 1;
                if ($privilege == "update") $privileges["update"] = 1;
                if ($privilege == "delete") $privileges["delete"] = 1;
            }
        }

        $result;
        switch ($_POST["user-action"]) {
            case 'add':
                $result = $admin->addSystemUser($user_data, $privileges);
                break;

            case 'update':
                $rslt = $admin->updateSystemUser($_POST["user-id"], $_POST["user-fname"], $_POST["user-lname"], $_POST["user-email"], $_POST["user-role"], $privileges);
                if (!$rslt) {
                    die(json_encode(array("success" => false, "message" => "Failed to update admission information!")));
                }
                $result = array("success" => true, "message" => "Successfully updated admission information!");
                break;
        }

        die(json_encode($result));
    }

    // For sales report on accounts dashboard
    elseif ($_GET["url"] == "salesReport") {
        if (!isset($_POST["admission-period"])) die(json_encode(array("success" => false, "message" => "Invalid input request for admission period!")));
        if (!isset($_POST["from-date"])) die(json_encode(array("success" => false, "message" => "Invalid input request for from date!")));
        if (!isset($_POST["to-date"])) die(json_encode(array("success" => false, "message" => "Invalid input request for to date!")));
        if (!isset($_POST["form-type"])) die(json_encode(array("success" => false, "message" => "Invalid input request for form type!")));
        if (!isset($_POST["purchase-status"])) die(json_encode(array("success" => false, "message" => "Invalid input request for purchase status!")));
        if (!isset($_POST["payment-method"])) die(json_encode(array("success" => false, "message" => "Invalid input request for payment method!")));

        if ((!empty($_POST["from-date"]) && empty($_POST["to-date"])) || (!empty($_POST["to-date"]) && empty($_POST["from-date"])))
            die(json_encode(array("success" => false, "message" => "Date range (From - To) must be set!")));

        $result = $admin->fetchAllFormPurchases($_POST);
        if (empty($result)) die(json_encode(array("success" => false, "message" => "No result found for given parameters!")));
        die(json_encode(array("success" => true, "message" => $result)));
    }

    //
    elseif ($_GET["url"] == "purchaseInfo") {
        if (!isset($_POST["_data"]) || empty($_POST["_data"]))
            die(json_encode(array("success" => false, "message" => "Invalid request!")));
        $data = $expose->validateNumber($_POST["_data"]);
        $result = $admin->fetchAllFormPurchases();
        if (empty($result)) die(json_encode(array("success" => false, "message" => "No result found in database!")));
        die(json_encode(array("success" => true, "message" => $result)));
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

    if ($_GET["url"] == "user-form") {
        if (!isset($_DELETE["user_key"]) || empty($_DELETE["user_key"])) {
            die(json_encode(array("success" => false, "message" => "Missing input field")));
        }

        $rslt = $admin->deleteSystemUser($_DELETE["user_key"]);

        if (!$rslt) {
            die(json_encode(array("success" => false, "message" => "Failed to delete user account!")));
        }

        die(json_encode(array("success" => true, "message" => "Successfully deleted user account!")));
    }
} else {
    http_response_code(405);
}
