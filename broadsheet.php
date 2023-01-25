<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["admin"]) && !empty($_SESSION["admin"])) {
} else {
  header("Location: login.php");
}

if (isset($_GET['logout'])) {
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

  header('Location: login.php');
}
?>
<?php
require_once('bootstrap.php');

use Src\Controller\AdminController;

$expose = new AdminController();
require_once('inc/page-data.php');

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <?= require_once("inc/head.php") ?>
</head>

<body>
  <?= require_once("inc/header.php") ?>

  <?= require_once("inc/sidebar.php") ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Broadsheet</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="applications.php">Applications</a></li>
          <li class="breadcrumb-item active">Broadsheet</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Recent Sales -->
        <div class="col-12">

          <div class="card recent-sales overflow-auto">

            <div class="card-body">
              <h5 class="card-title">Broadsheet</h5>
              <form id="fetchDataForm" class="mb-4">
                <div class="row">
                  <div class="col-3">
                    <label for="cert-type" class="form-label">Certificate Type</label>
                    <select name="cert-type" id="cert-type" class="form-select form-select-sm">
                      <option value="" hidden>Choose Certificate</option>
                      <option value="WASSCE">WASSCE/NECO</option>
                      <option value="SSSCE">SSSCE/GBCE</option>
                      <option value="Baccalaureate">BACCALAUREATE</option>
                      <option value="ALL">ALL</option>
                    </select>
                  </div>
                  <div class="col-3">
                    <label for="prog-type" class="form-label" style="visibility: hidden;">Programme Category</label>
                    <button type="submit" class="btn mb-4 btn-outline-primary btn-sm">Fetch Data</button>
                  </div>
                </div>
              </form>
              <div id="info-output"></div>
              <table class="table table-borderless datatable table-striped table-hover">
                <thead>
                  <tr class="table-dark">
                    <th scope="col">#</th>
                    <th scope="col" colspan="1">Full Name</th>
                    <th scope="col" colspan="1">Programme</th>
                    <th scope="col" colspan="4" style="text-align: center;">Core Subjects</th>
                    <th scope="col" colspan="4" style="text-align: center;">Elective Subjects</th>
                  </tr>
                  <tr class="table-grey">
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Core Mathematics">CM</th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="English Language">EL</th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Integrated Science">IS</th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Social Studies">SS</th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 1">E1</th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 2">E2</th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 3">E3</th>
                    <th scope="col" style="background-color: #999; text-align: center" data-bs-toggle="tooltip" data-bs-placement="top" data-bs-title="Elective 4">E4</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <div class="mt-4" id="down-bs" style="display: none;float:right">
                <button class="btn btn-primary" id="download-bs">Download Broadsheet</button>
              </div>
              <div class="clearfix"></div>
            </div>

          </div>
        </div><!-- End Recent Sales -->

        <!-- Right side columns -->
        <!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <?= require_once("inc/footer-section.php") ?>

  <script>
    $(document).ready(function() {

      $("#download-bs").click(function() {
        let data = {
          'cert-type': $('#cert-type').val()
        }

        if ($('#cert-type').val() == "") {
          alert("Missing Values! Choose Certificate type and Program type");
          return;
        }

        $.ajax({
          type: "POST",
          url: "endpoint/downloadBS",
          data: data,
          success: function(result) {
            console.log(result);
            if (result.success) window.open(result.message, '_blank');
            else alert(result.message);
          },
          error: function(error) {
            console.log(error);
          }
        });
      })

      var fetchBroadsheet = function() {
        data = {
          "cert-type": $("#cert-type").val()
        }

        $.ajax({
          type: "POST",
          url: "endpoint/getBroadsheetData",
          data: data,
          success: function(result) {
            console.log(result);

            if (result.success) {
              $("tbody").html('');
              $.each(result.message, function(index, value) {
                $("tbody").append(
                  '<tr>' +
                  '<th scope="row">' + (index + 1) + '</th>' +
                  '<td>' + value.app_pers.first_name + ' ' + value.app_pers.last_name + '</td>' +
                  '<td>' + value.app_pers.programme + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[0].subject + '">' + value.sch_rslt[0].grade + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[1].subject + '">' + value.sch_rslt[1].grade + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[2].subject + '">' + value.sch_rslt[2].grade + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[3].subject + '">' + value.sch_rslt[3].grade + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[4].subject + '">' + value.sch_rslt[4].grade + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[5].subject + '">' + value.sch_rslt[5].grade + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[6].subject + '">' + value.sch_rslt[6].grade + '</td>' +
                  '<td style="cursor: help; text-align: center" title="' + value.sch_rslt[7].subject + '">' + value.sch_rslt[7].grade + '</td>' +
                  '</tr>');
              });
              $("#down-bs").show();
            } else {
              $("tbody").html('');
              $("#info-output").html(
                '<div class="alert alert-info alert-dismissible fade show" role="alert">' +
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
      }
      $("#fetchDataForm").on("submit", function(e) {
        e.preventDefault();
        fetchBroadsheet();
      });

    });
  </script>

</body>

</html>