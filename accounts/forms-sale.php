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
</head>

<body>
  <?= require_once("../inc/header.php") ?>

  <?= require_once("../inc/sidebar.php") ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Forms Sale</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Forms Sale</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-12">
          <div class="card recent-sales overflow-auto">

            <div class="filter">
              <span class="icon export-excel" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Export Excel">
                <img src="../assets/img/icons8-microsoft-excel-2019-48.png" alt="" style="width: 24px;">
              </span>
              <span class="icon download-pdf" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download PDF">
                <img src="../assets/img/icons8-pdf-48.png" alt="" style="width: 24px;">
              </span>
            </div>

            <div class="card-body">
              <h5 class="card-title">Purchases</h5>
              <hr>
              <!-- Left side columns -->
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
                      $result = $admin->getFormTypes();
                      foreach ($result as $value) {
                      ?>
                        <option value="<?= $value["id"] ?>"><?= $value["name"] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>

                  <div class="col-2 col-md-2 col-sm-12 mt-2">
                    <label for="purchase-status" class="form-label">Purchase Status</label>
                    <select name="purchase-status" id="purchase-status" class="form-select">
                      <option value="" hidden>Choose</option>
                      <option value="All">All</option>
                      <option value="COMPLETED">COMPLETED</option>
                      <option value="FAILED">FAILED</option>
                      <option value="PENDING">PENDING</option>
                    </select>
                  </div>

                  <div class="col-2 col-md-2 col-sm-12 mt-2">
                    <label for="payment-method" class="form-label">Payment Method</label>
                    <select name="payment-method" id="payment-method" class="form-select">
                      <option value="" hidden>Choose</option>
                      <option value="All">All</option>
                      <option value="CARD">CARD</option>
                      <option value="CASH">CASH</option>
                      <option value="MOMO">MOMO</option>
                    </select>
                  </div>

                </div>
              </form>

              <div class="mt-4" style="margin-top: 50px !important; display: flex; justify-content: space-between">
                <h4>Total: <span id="totalData"></span></h4>
                <div id="alert-output">
                </div>
              </div>

              <div style="margin-top: 50px !important">
                <table class="table table-borderless table-striped table-hover">

                  <thead class="table-dark">
                    <tr>
                      <th scope="col">Transaction ID</th>
                      <th scope="col">Name</th>
                      <th scope="col">Phone Number</th>
                      <th scope="col">Admission Period</th>
                      <th scope="col">Form Type</th>
                      <th scope="col">Status</th>
                      <th scope="col">Payment Method</th>
                      <th scope="col">Date</th>
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
      </div><!-- End Left side columns -->

      <!-- Purchase info Modal -->
      <div class="modal fade" id="purchaseInfoModal" tabindex="-1" aria-labelledby="purchaseInfoModal" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="purchaseInfoModalTitle">Purchase Information</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <div class="mb-4 row">
                <div class="mb-3 col-5">
                  <div class="input-group">
                    <span class="input-group-text" id="basic-addon3">Trans. ID: </span>
                    <input disabled type="text" class="form-control" id="p-transID" aria-describedby="basic-addon3">
                  </div>
                </div>
                <div class="mb-3 col-7">
                  <div class="input-group">
                    <span class="input-group-text" id="basic-addon3">Admission Period: </span>
                    <input disabled type="text" class="form-control" id="p-admisP" aria-describedby="basic-addon3">
                  </div>
                </div>
              </div>
              <fieldset class="mb-4 mt-4">
                <legend>Personal</legend>
                <div class="row">
                  <div class="mb-3 col">
                    <label for="p-name" class="form-label">Name</label>
                    <input disabled type="text" class="form-control" id="p-name">
                  </div>
                  <div class="mb-3 col">
                    <label for="p-country" class="form-label">Country</label>
                    <input disabled type="text" class="form-control" id="p-country">
                  </div>
                </div>
                <div class="row">
                  <div class="mb-3 col">
                    <label for="p-email" class="form-label">Email Address</label>
                    <input disabled type="text" class="form-control" id="p-email">
                  </div>
                  <div class="mb-3 col">
                    <label for="p-phoneN" class="form-label">Phone Number</label>
                    <input disabled type="text" class="form-control" id="p-phoneN">
                  </div>
                </div>
              </fieldset>
              <fieldset class="mb-4">
                <legend>Form</legend>
                <div class="row">
                  <div class="mb-3 col">
                    <label for="p-appN" class="form-label">App Number</label>
                    <input disabled type="text" class="form-control" id="p-appN">
                  </div>
                  <div class="mb-3 col">
                    <label for="p-pin" class="form-label">PIN</label>
                    <input disabled type="text" class="form-control" id="p-pin">
                  </div>
                  <div class="mb-3 col">
                    <label for="p-status" class="form-label">Status</label>
                    <input disabled type="text" class="form-control" id="p-status">
                  </div>
                </div>
                <div class="row">
                  <div class="mb-3 col">
                    <label for="p-vendor" class="form-label">Vendor</label>
                    <input disabled type="text" class="form-control" id="p-vendor">
                  </div>
                  <div class="mb-3 col">
                    <label for="p-formT" class="form-label">Form Type</label>
                    <input disabled type="text" class="form-control" id="p-formT">
                  </div>
                  <div class="mb-3 col">
                    <label for="p-payM" class="form-label">Payment Method</label>
                    <input disabled type="text" class="form-control" id="p-payM">
                  </div>
                </div>
              </fieldset>
            </div>
            <div class="modal-footer">
              <div class="row" style="width:100% !important">
                <form id="sendPurchaseInfo" method="post" style="display: flex; justify-content:center">
                  <button type="submit" class="btn btn-success" style="padding:15px !important">Send application login info</button>
                  <input type="hidden" name="sendTransID" id="sendTransID" value="">
                </form>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right side columns -->
      <!-- End Right side columns -->

    </section>

  </main><!-- End #main -->

  <?= require_once("../inc/footer-section.php") ?>
  <script>
    $(document).ready(function() {

      // when 
      $(".form-select, .form-control").change("blur", function(e) {
        e.preventDefault();
        $("#reportsForm").submit();
      });

      $("#reportsForm").on("submit", function(e) {
        e.preventDefault();

        $.ajax({
          type: "POST",
          url: "../endpoint/salesReport",
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
                  '<td>' + value.id + '</td>' +
                  '<td>' + value.fullName + '</td>' +
                  '<td>' + value.phoneNumber + '</td>' +
                  '<td>' + value.admissionPeriod + '</td>' +
                  '<td>' + value.formType + '</td>' +
                  '<td>' + value.status + '</td>' +
                  '<td>' + value.paymentMethod + '</td>' +
                  '<td>' + value.added_at + '</td>' +
                  '<td>' +
                  '<button id="' + value.id + '" class="btn btn-xs btn-primary openPurchaseInfo" data-bs-toggle="modal" data-bs-target="#purchaseInfoModal">View</button>' +
                  '</td>' +
                  '</tr>'
                );
              });
            } else {
              $("tbody").html('');
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

      $(".openPurchaseInfo").click(function(e) {

        let data = {
          _data: $(this).attr("id")
        }
        console.log(this.id)
        $.ajax({
          type: "POST",
          url: "../endpoint/purchaseInfo",
          data: data,
          success: function(result) {
            console.log(result);

            if (result.success) {
              $("#p-transID, #").val(result.message.transID);
              $("#p-admisP").val(result.message.admisP);
              $("#p-name").val(result.message.name);
              $("#p-country").val(result.message.country);
              $("#p-email").val(result.message.email);
              $("#p-phoneN").val(result.message.phoneN);
              $("#p-appN").val(result.message.appN);
              $("#p-pin").val(result.message.pin);
              $("#p-status").val(result.message.status);
              $("#p-vendor").val(result.message.vendor);
              $("#p-formT").val(result.message.formT);
              $("#p-payM").val(result.message.payM);
              $("#sendTransID").val(result.message.transID);
            } else {
              alert(result.message);
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