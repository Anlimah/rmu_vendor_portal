<?php
session_start();

if (!isset($_GET['exttrid']) || empty($_GET['exttrid'])) header('Location: index.php?msg=Invalid request');

if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: ../login.php");
}

if (isset($_SESSION["vendor_id"]) && !empty($_SESSION["vendor_id"]))
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
    <style>
        body {
            /*was 000*/
            font-family: "Ubuntu", sans-serif !important;
            font-weight: 300;
            -webkit-overflow-scrolling: touch;
            overflow: auto;
            line-height: 1;
            background-color: #f9f9f9 !important;
            color: #282828 !important;
            font-size: 16px !important;
        }

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
            font-weight: 400;
            width: 100%;
        }

        .purchase-card-footer {
            width: 100% !important;
        }

        .bg-img {
            width: 100%;
            height: 100%;
            background-image: url(../assets/img/logo.png);
            background-repeat: no-repeat;
            background-position: contain;
            background-size: 10%;
            background-color: rgba(255, 255, 255, 0.5);
            opacity: 0.5;
            z-index: 9999;
            position: absolute;
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@500&family=Roboto+Mono:wght@700&family=Ubuntu:wght@400;700&display=swap" rel="stylesheet">
</head>

<body>

    <main id="main" class="main">

        <section class="section dashboard">
            <div class="flex-card">
                <div class="form-card card" style="max-width: 800px !important;">

                    <div class="flex-column">
                        <div class="purchase-card-header flex-row" style="align-items:baseline; justify-content: space-between">
                            <h1>REGIONAL MARITIME UNIVERSITY</h1>
                            <h1 style="font-size:medium !important;">Receipt Number: RMUHF<?= $data[0]["id"] ?></h1>
                        </div>
                        <div class="flex-row" style="align-items: baseline; justify-content:space-around;">
                            <p style="font-size:medium !important;">Date Issued: <?= date("jS F, Y") . " - " . date("h:i:s A") ?></p>
                        </div>
                    </div>

                    <div class="purchase-card-body">
                        <div class="pay-status" style="align-items: baseline;">
                            <?php if (!empty($data)) { ?>
                                <table style="width:100%;border-collapse: collapse;" class="mb-4">
                                    <tr>
                                        <td style="width: 150px; background: #f1f1f1;text-align: right; padding: 10px;"><b>VENDOR:</b></td>
                                        <td colspan="2" style="text-align: left; padding: 10px;"><b><?= $data[0]["company"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 10px;"><b>PRICE:</b></td>
                                        <td style="text-align: left; padding: 10px;"><b><?= $data[0]["amount"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 10px;"><b>APPLICATION NO:</b></td>
                                        <td style="text-align: left; padding: 10px;"><b><?= "RMU-" . $data[0]["app_number"] ?></b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 10px;"><b>PIN NO:</b></td>
                                        <td style="text-align: left; padding: 10px;"><b><?= $data[0]["pin_number"] ?></b></td>
                                    </tr>
                                    <tr style="border-top: 1px solid rgb(155, 155, 155)">
                                        <td style="background: #f1f1f1;text-align: right; padding: 10px;">INSTITUTION:</td>
                                        <td style="text-align: left; padding: 10px;"><b>REGIONAL MARITIME UNIVERSITY</b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 10px;">CAMPUS:</td>
                                        <td style="text-align: left; padding: 10px;"><b>MAIN</b></td>
                                    </tr>
                                    <tr>
                                        <td style="background: #f1f1f1;text-align: right; padding: 10px;">FORM NAME:</td>
                                        <td style="text-align: left; padding: 10px;"><b><?= $data[0]["info"] . " - " . strtoupper($data[0]["name"]) ?></b></td>
                                    </tr>
                                </table>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <script>
        document.addEventListener("DOMContentLoaded", () => {
            window.print();
            window.close();
            
        })
    </script>

</body>

</html>