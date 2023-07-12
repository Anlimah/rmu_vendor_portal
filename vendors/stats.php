<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: login.php");
}

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
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>
    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Daily Transactions</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Home</a></li>
                    <li class="breadcrumb-item active">Daily Transactions</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">

            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        <?php
                        $summary = $admin->fetchVendorSummary($_SESSION["vendor_id"]);
                        $admissionInfo = $admin->fetchCurrentAdmissionPeriod();
                        ?>

                        <div class="card-body">
                            <h5 class="card-title">Summary (<?= $admissionInfo[0]["info"] ?>)</h5>

                            <!-- Form Types -->
                            <div class="form-types">
                                <div class="row">
                                    <?php foreach ($summary["form-types"] as $form) { ?>
                                        <!-- Masters Card -->
                                        <div class="col-xxl-4 col-md-4">
                                            <div class="card">
                                                <div class="card-body">
                                                    <h6 style="font-size: 18px; font-weight: 650; margin-top:20px"><?= $form["name"] ?></h6>
                                                    <div class="mt-2" style="display:flex; justify-content:space-between">

                                                        <div style="display: flex; flex-direction:column; justify-content:flex-start">
                                                            <span style="font-size: 16px;"><?= $form["total_num"] ?></span>
                                                            <span class="text-muted small">COUNT</span>
                                                        </div>

                                                        <div style="display: flex; flex-direction:column; justify-content:flex-start">
                                                            <h5 style="padding-bottom: 0; margin-bottom:0;">
                                                                <span class="small">GH</span>&#162;<span class="small"><?= $form["total_amount"] ? $form["total_amount"] : "0.00" ?></span>
                                                            </h5>
                                                            <span class="text-muted small">AMOUNT</span>
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div><!-- End Masters Card -->
                                    <?php } ?>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>
            </div><!-- End Transactions Summary row -->

            <!-- Transactions Table -->
            <div class="row">
                <div class="col-12">
                    <div class="card recent-sales overflow-auto">

                        <div class="card-body">
                            <h5 class="card-title">Transactions</h5>

                            <form id="reportsForm" method="post">
                                <div class="row">

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="admission-period" class="form-label">Admission Period</label>
                                        <select name="admission-period" id="admission-period" class="form-select">
                                            <option value="" hidden>Choose</option>
                                            <option value="All">All</option>
                                            <?php
                                            $result = $admin->fetchAllAdmissionPeriod();
                                            foreach ($result as $value) {
                                            ?>
                                                <option value="<?= $value["id"] ?>"><?= $value["info"] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="from-date" class="form-label">From (Date)</label>
                                        <input type="date" name="from-date" id="from-date" class="form-control">
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="to-date" class="form-label">To (Date)</label>
                                        <input type="date" name="to-date" id="to-date" class="form-control">
                                    </div>

                                    <div class="col-2 col-md-2 col-sm-12 mt-2">
                                        <label for="form-type" class="form-label">Form Type</label>
                                        <select name="form-type" id="form-type" class="form-select">
                                            <option value="" hidden>Choose</option>
                                            <option value="All">All</option>
                                            <?php
                                            $result = $admin->getAvailableForms();
                                            foreach ($result as $value) {
                                            ?>
                                                <option value="<?= $value["id"] ?>"><?= $value["name"] ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                            </form>

                            <div style="margin-top: 10px !important">
                                <table class="table table-borderless table-striped table-hover" id="dataT">

                                    <thead class="table-dark">
                                        <tr>
                                            <th scope="col">S/N</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Transaction ID</th>
                                            <th scope="col">Name</th>
                                            <th scope="col">Phone Number</th>
                                            <th scope="col">Admission Period</th>
                                            <th scope="col">Form Bought</th>
                                            <th scope="col">Status</th>
                                            <th scope="col">Payment Method</th>
                                            <th scope="col">Action</th>
                                        </tr>
                                    </thead>

                                    <tbody>
                                    </tbody>

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div><!-- Transactions List row -->

        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>
    <script>
        $("dataTable-top").hide();
    </script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on({
                ajaxStart: function() {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    // Hide it after 3 seconds
                    $.LoadingOverlay("hide");
                }
            });

            $("#reportsForm").on("submit", function(e, d) {
                e.preventDefault();
                triggeredBy = 1;

                $.ajax({
                    type: "POST",
                    url: "../endpoint/dailySalesByVendor",
                    data: new FormData(this),
                    processData: false,
                    contentType: false,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("#totalData").text(result.message.length);
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                $("tbody").append(
                                    '<tr>' +
                                    '<td>' + (index + 1) + '</td>' +
                                    '<td>' + value.added_at + '</td>' +
                                    '<td>' + value.id + '</td>' +
                                    '<td>' + value.fullName + '</td>' +
                                    '<td>' + value.phoneNumber + '</td>' +
                                    '<td>' + value.admissionPeriod + '</td>' +
                                    '<td>' + value.formType + '</td>' +
                                    '<td>' + value.status + '</td>' +
                                    '<td>' + value.paymentMethod + '</td>' +
                                    '<td>' +
                                    '<button id="' + value.id + '" class="btn btn-xs btn-primary openPurchaseInfo" data-bs-toggle="modal" data-bs-target="#purchaseInfoModal">View</button>' +
                                    '</td>' +
                                    '</tr>'
                                );
                            });
                        } else {
                            $("#alert-output").html('');
                            $("#alert-output").html(
                                '<div class="alert alert-danger alert-dismissible fade show" role="alert">' +
                                '<i class="bi bi-info-circle me-1"></i>' + result.message +
                                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                                '</div>'
                            );
                        }

                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });
        });
    </script>
</body>

</html>