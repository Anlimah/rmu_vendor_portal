<?php
session_start();

if (!isset($_GET['status']) || !isset($_GET['exttrid'])) header('Location: index.php?status=invalid');
if (empty($_GET['status']) || empty($_GET['exttrid'])) header('Location: index.php?status=invalid');

if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: ../login.php");
}

if (isset($_SESSION["loginSuccess"]) && $_SESSION["loginSuccess"] == true && isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]))
    $trans_id = $_GET["exttrid"];
else header("Location: index.php");

if (isset($_GET['logout']) || strtolower($_SESSION["role"]) != "vendors") {
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


require_once('../bootstrap.php');

use Src\Controller\AdminController;

$admin = new AdminController();

use Src\Controller\ExposeDataController;

$expose = new ExposeDataController();

$data = $expose->getApplicationInfo($_GET["exttrid"]);
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
            flex-direction: row !important;
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
            <div class="flex-card">
                <div class="form-card card" style="max-width: 800px !important;">

                    <div class="purchase-card-header flex-row">
                        <h1>Applicant Receipt</h1>
                        <b><span class="bi bi-x-lg me-5 text-danger" style="cursor: pointer;" onclick="window.location.href = 'sell.php'"></span></b>
                    </div>

                    <hr style="color:#999">

                    <div class="purchase-card-body">
                        <div class="pay-status" style="margin: 0px 5%;" style="align-items: baseline;">
                            <?php if (!empty($data)) { ?>
                                <table style="width:100%;border: 1px solid rgb(155, 155, 155); border-collapse: collapse;" class="mb-4">
                                    <tr>
                                        <td style="width: 120px; background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>VENDOR:</b></td>
                                        <td colspan="2" style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["company"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>PRICE:</b></td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["amount"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>APPLICATION NO:</b></td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= "RMU-" . $data[0]["app_number"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px;"><b>PIN NO:</b></td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["pin_number"] ?></b></td>
                                    </tr>
                                    <tr style="border-top: 1px solid rgb(155, 155, 155)">
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px; padding-top:30px">INSTITUTION:</td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b>REGIONAL MARITIME UNIVERSITY</b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 5px; font-size: 11px">FORM NAME:</td>
                                        <td style="text-align: left; padding: 5px; font-size: 11px;"><b><?= $data[0]["info"] . "-" . strtoupper($data[0]["name"]) ?></b></td>
                                    </tr>
                                </table>
                                <center>
                                    <button class="btn btn-primary"><b>Print</b></button>
                                </center>
                            <?php } else { ?>
                                <div style="width: 100%;height: 100%; text-align:center">No Data available</div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
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

            var count = 1;
            var intervalId = setInterval(() => {
                $("#timer").html("Resend code <b>(" + count + " sec)</b>");
                count = count - 1;
                if (count <= 0) {
                    clearInterval(intervalId);
                    $(' #timer').hide();
                    $('#resend-code').removeClass("hide").addClass("display");
                    return;
                }
            }, 1000);

            $("#resend-code").click(function(e) {
                e.preventDefault();
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
                            count = 1;
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
                        flashMessage("alert-danger", error);
                    }
                });
            });

            $("#verifyOTPCodeForm").on("submit", function(e) {
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
                                window.location.href = result.message;
                            else
                                window.location.href = "confirm.php?status=000&exttrid=" + result.exttrid;
                        } else {
                            flashMessage("alert-danger", result.message);
                        }
                    },
                    error: function(error) {
                        flashMessage("alert-danger", error);
                    }
                });
            });

            $(document).on({
                ajaxStart: function() {
                    if (triggeredBy == 1) $("#resend-code").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> sending...');
                    if (triggeredBy == 2) $("#verifyCodeBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
                },
                ajaxStop: function() {
                    if (triggeredBy == 1) $("#resend-code").prop("disabled", false).html('Resend code');
                    if (triggeredBy == 2) $("#verifyCodeBtn").prop("disabled", false).html('Verify');
                }
            });

            $("#num1").focus();

            $(".num").on("keyup", function() {
                if (this.value.length == 4) {
                    $(this).next(":input").focus().select(); //.val(''); and as well clesr
                }
            });

            $("input[type='text']").on("click", function() {
                $(this).select();
            });

            function flashMessage(bg_color, message) {
                const flashMessage = document.getElementById("flashMessage");

                flashMessage.classList.add(bg_color);
                flashMessage.innerHTML = message;

                setTimeout(() => {
                    flashMessage.style.visibility = "visible";
                    flashMessage.classList.add("show");
                }, 500);

                setTimeout(() => {
                    flashMessage.classList.remove("show");
                    setTimeout(() => {
                        flashMessage.style.visibility = "hidden";
                    }, 500);
                }, 5000);
            }
        });
    </script>

</body>

</html>