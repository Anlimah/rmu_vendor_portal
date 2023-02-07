<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: login.php");
}


if (isset($_GET['logout']) || strtolower($_SESSION["role"]) != "accounts") {
    session_destroy();
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }

    header('Location: ../login.php');
}
?>
<?php
require_once('../bootstrap.php');

use Src\Controller\AdminController;

$admin = new AdminController();
require_once('../inc/page-data.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= require_once("../inc/head.php") ?>
    <style>
        .hide {
            display: none;
        }

        .display {
            display: block;
        }

        #wrapper {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            justify-content: space-between;
            width: 100% !important;
            height: 100% !important;
        }

        .flex-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .flex-container>div {
            height: 100% !important;
            width: 100% !important;
        }

        .flex-column {
            display: flex !important;
            flex-direction: column !important;
        }

        .flex-row {
            display: flex !important;
            flex-direction: column !important;
        }

        .justify-center {
            justify-content: center !important;
        }

        .justify-space-between {
            justify-content: space-between !important;
        }

        .align-items-center {
            align-items: center !important;
        }

        .align-items-baseline {
            align-items: baseline !important;
        }

        .flex-card {
            display: flex !important;
            justify-content: center !important;
            flex-direction: row !important;
        }

        .form-card {
            height: 100% !important;
            max-width: 425px !important;
            padding: 15px 10px 20px 10px !important;
        }

        .flex-card>.form-card {
            height: 100% !important;
            width: 100% !important;
        }

        .purchase-card-header {
            padding: 0 !important;
            width: 100% !important;
            height: 40px !important;
        }

        .purchase-card-header>h1 {
            font-size: 22px !important;
            font-weight: 600 !important;
            color: #003262 !important;
            text-align: center;
            width: 100%;
        }

        .purchase-card-step-info {
            color: #003262;
            padding: 0px;
            font-size: 14px;
            font-weight: 400;
            width: 100%;
        }

        .purchase-card-footer {
            width: 100% !important;
        }
    </style>
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Forms Sale</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Sell Forms</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">
            <?php
            if (isset($_SESSION['verifySMSCode']) && $_SESSION['verifySMSCode'] == true) {
                if (!isset($_SESSION["_verifySMSToken"])) {
                    $rstrong = true;
                    $_SESSION["_verifySMSToken"] = hash('sha256', bin2hex(openssl_random_pseudo_bytes(64, $rstrong)));
                }
            ?>
                <!-- Left side columns -->
                <div class="row" style="display:flex !important; flex-direction:row !important; justify-content: center !important; align-items: center">
                    <div class="flex-card">
                        <div class="form-card card">
                            <div class="purchase-card-header">
                                <h1>Verify Phone Number</h1>
                            </div>

                            <hr style="color:#999">

                            <div class="purchase-card-body" style="margin: 0px 10%;">
                                <form action="#" id="step1Form" method="post" enctype="multipart/form-data">
                                    <p class="mb-4">Enter the verification code sent to your phone.</p>
                                    <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-around !important; align-items:center">
                                        <input class="form-control num me-2" type="text" maxlength="4" style="padding: 10px 10px;text-align:center" name="code" id="code" placeholder="XXXX" required>
                                        <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px;">Verify</button>
                                    </div>
                                    <input class="form-control" type="hidden" name="_vSMSToken" id="_vSMSToken" value="<?= $_SESSION["_verifySMSToken"] ?>">
                                </form>
                                <div class="purchase-card-footer flex-row" style="align-items: flex-end;">
                                    <span id="timer"></span>
                                    <button id="resend-code" class="btn btn-outline-dark btn-xs hide">Resend code</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><!-- End Left side columns -->


            <?php
            }
            if (isset($_SESSION['vendor_id']) && !empty($_SESSION['vendor_id'])) {
            ?>

                <div class="purchase-card-body">
                    <form id="step1Form" method="post" enctype="multipart/form-data">
                        <div class="flex-column align-items-center">
                            <div class="flex-row justify-space-between">
                                <div>
                                    <div class="mb-4">
                                        <label class="form-label" for="first_name">First Name</label>
                                        <input name="first_name" id="first_name" title="Provide your first name" class="form-control" type="text" placeholder="Type your first name" required>
                                    </div>
                                    <div class="mb-4">
                                        <label class="form-label" for="last_name">Last Name</label>
                                        <input name="last_name" id="last_name" title="Provide your last name" class="form-control" type="text" placeholder="Type your last name" required>
                                    </div>
                                </div>
                                <div>
                                    <div class="mb-4">
                                        <label class="form-label" for="gender">Form type</label>
                                        <select title="Select the type of form you want to purchase." class="form-select form-select-sm" name="form_type" id="form_type" required>
                                            <option selected disabled value="">Choose...</option>
                                            <?php
                                            $data = $expose->getFormTypes();
                                            foreach ($data as $ft) {
                                            ?>
                                                <option value="<?= $ft['name'] ?>"><?= $ft['name'] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                    <div class="mb-4">
                                        <div style="display:flex !important; flex-direction:row !important; justify-content: flex-start !important;">
                                            <label class="form-label" for="country" style="margin-right: 10px; width: 45%">Country Code</label>
                                            <label class="form-label" style="float:left" for="phone-number">Phone Number</label>
                                        </div>
                                        <div style="display:flex !important; flex-direction:row !important; justify-content: space-between !important">
                                            <input name="country" id="country" value="<?= '(' . COUNTRIES[83]["code"] . ') ' . COUNTRIES[83]["name"]  ?>" title="Choose country and country code" class="form-control form-control-sm" list="address-country-list" style="margin-right: 10px; width: 60%" placeholder="Type for options" required>
                                            <datalist id="address-country-list">
                                                <?php
                                                foreach (COUNTRIES as $cn) {
                                                    echo '<option value="(' . $cn["code"] . ') ' . $cn["name"] . '">(' . $cn["code"] . ') ' . $cn["name"] . '</option>';
                                                }
                                                ?>
                                            </datalist>
                                            <input name="phone_number" id="phone_number" maxlength="10" title="Provide your Provide Number" class="form-control form-control-sm" style="width: 70%" type="tel" placeholder="0244123123" required>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <div>
                                <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px; width:100%">Submit</button>
                                <input type="hidden" name="_v1Token" value="<?= $_SESSION["_vendor1Token"]; ?>">
                            </div>
                        </div>
                    </form>
                </div>
            <?php } ?>

            <div class="row" style="display:flex !important; flex-direction:row !important; justify-content: center !important; align-items: center">
                <div class="flex-card">
                    <div class="form-card card">
                        <div class="purchase-card-header">
                            <h1>Verify Phone Number</h1>
                        </div>

                        <hr style="color:#999">

                        <div class="purchase-card-body" style="margin: 0px 10%;">
                            <form id="sendVCForm" method="post">
                                <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-around !important; align-items:center">
                                    <button class="btn btn-primary" type="submit" id="submitBtn" style="padding: 10px 10px;">Send verification code</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div><!-- End Left side columns -->
        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>
    <script>
        $(document).ready(function() {
            //get variable(parameters) from url
            function getUrlVars() {
                var vars = {};
                var parts = window.location.href.replace(
                    /[?&]+([^=&]+)=([^&]*)/gi,
                    function(m, key, value) {
                        vars[key] = value;
                    }
                );
                return vars;
            }

            var triggeredBy = 0;

            var count = 60;
            var intervalId = setInterval(() => {
                $("#timer").html("Resend code <b>(" + count + " sec)</b>");
                count = count - 1;
                if (count <= 0) {
                    clearInterval(intervalId);
                    $('#timer').hide();
                    $('#resend-code').removeClass("hide").addClass("display");
                    return;
                }
            }, 1000); //1000 will  run it every 1 second

            $("#resend-code").click(function() {
                triggeredBy = 1;

                let data = {
                    resend_code: "sms",
                    _vSMSToken: $("#_vSMSToken").val()
                };

                $.ajax({
                    type: "POST",
                    url: "../endpoint/resend-code",
                    data: data,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            flashMessage("alert-success", result.message);

                            clearInterval(intervalId);
                            $("#timer").show();
                            $('#resend-code').removeClass("display").addClass("hide");

                            count = 60;
                            intervalId = setInterval(() => {
                                $("#timer").html("Resend code <b>(" + count + " sec)</b>");
                                count = count - 1;
                                if (count <= 0) {
                                    clearInterval(intervalId);
                                    $('#timer').hide();
                                    $('#resend-code').removeClass("hide").addClass("display").attr("disabled", false);
                                    return;
                                }
                            }, 1000); /**/
                        } else {
                            flashMessage("alert-danger", result.message);
                        }
                    },
                    error: function(error) {
                        console.log(error.statusText);
                    }
                });
            });

            $("#sendVCForm").on("submit", function(e) {
                e.preventDefault();
                alert("OK")
                triggeredBy = 3;

                $.ajax({
                    type: "POST",
                    url: "../endpoint/send-v-vc",
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            window.location.reload();
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {}
                });
            });

            $("#step1Form").on("submit", function(e) {
                e.preventDefault();
                triggeredBy = 2;
                var url = "";
                if (getUrlVars()["verify"] == "vendor") {
                    url = "verifyVendor";
                } else if (getUrlVars()["verify"] == "customer") {
                    url = "verifyCustomer";
                } else {
                    return;
                }

                $.ajax({
                    type: "POST",
                    url: "../endpoint/" + url,
                    data: new FormData(this),
                    contentType: false,
                    cache: false,
                    processData: false,
                    success: function(result) {
                        console.log(result);
                        if (result.success) {
                            if (url == "verifyVendor")
                                window.location.href = "./";
                            else
                                window.location.href = "confirm.php?status=000&exttrid=" + result.exttrid;
                        } else {
                            alert(result.message);
                        }
                    },
                    error: function(error) {}
                });
            });

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 1) $("#resend-code").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> sending...');
                    if (triggeredBy == 3) $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                    else $("#submitBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 1) $("#resend-code").prop("disabled", false).html('Resend code');
                    if (triggeredBy == 3) $("#submitBtn").prop("disabled", false).html('Send verification code');
                    else $("#submitBtn").prop("disabled", false).html('Verify');
                }
            });

            $("#num1").focus();

            $(".num").on("keyup", function() {
                if (this.value) {
                    $(this).next(":input").focus().select(); //.val(''); and as well clesr
                }
            });

            $("input[type='text']").on("click", function() {
                $(this).select();
            });
        });
    </script>

</body>

</html>