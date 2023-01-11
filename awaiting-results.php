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
      <h1>Applications</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item"><a href="applications.php">Applications</a></li>
          <li class="breadcrumb-item active">Awaiting</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Recent Sales -->
        <div class="col-12">
          <div style="width: 100% !important">
            <div style="display: flex; flex-direction:row; justify-content:center;">
              <!-- Admitted Students Card -->
              <div class="col-xxl-3 col-md-3" style="width: 500px">
                <div class="card info-card">
                  <div class="card-body">
                    <h5 class="card-title" style="text-align: center;">Upload Results Datasheet</h5>
                    <div style="display: flex; flex-direction:column; align-items: center; justify-content:center;">
                      <div id="data-upload-form">
                        <p id="upload-notification" class="text-success"></p>
                        <form id="upload-awaiting-form" action="" method="post">
                          <label for="awaiting-ds" class="btn btn-primary" id="uploadBtn">Upload</label>
                          <input type="file" name="awaiting-ds" id="awaiting-ds" style="display: none;" accept=".xlsx,.xls,pplication/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                          <input type="hidden" name="action" value="uad">
                          <input type="hidden" name="startRow" value="1">
                          <input type="hidden" name="endRow" value="0">
                        </form>
                      </div>
                      <div id="data-process-info" class="mt-4">
                        <ol class="list-group list-group-horizontal" style="width:100% !important; font-size: 12px !important; font-family: Verdana, Arial, Tahoma, Serif !important;">
                          <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-2">
                              <div class="fw-bold">Total</div>
                            </div>
                            <span class="badge bg-warning rounded-pill">14</span>
                          </li>
                          <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-2">
                              <div class="fw-bold">Success</div>
                            </div>
                            <span class="badge bg-success rounded-pill">14</span>
                          </li>
                          <li class="list-group-item d-flex justify-content-between align-items-start">
                            <div class="ms-2 me-auto">
                              <div class="fw-bold">Errors</div>
                            </div>
                            <span class="badge bg-danger rounded-pill">14</span>
                          </li>
                        </ol>
                        <div class="error-info">
                          <table>
                            <thead>
                              <tr class="table-dark">
                                <th scope="col">#</th>
                                <th scope="col" colspan="1">Index Number</th>
                                <th scope="col" colspan="1">Message</th>
                              </tr>
                            </thead>
                            <tbody>
                              <tr>
                                <td>123456789</td>
                                <td>Applicant index number doesn't match any record in database</td>
                              </tr>
                              <tr>
                                <td>123456789</td>
                                <td>Applicant index number doesn't match any record in database</td>
                              </tr>
                            </tbody>
                          </table>
                        </div>
                      </div>

                    </div>
                  </div>
                </div>
              </div>
              <!-- End Admitted Students Card -->
            </div>
          </div>

          <div class="card recent-sales overflow-auto">

            <div class="card-body" style="display: none;">
              <h5 class="card-title">Admit Applicants</h5>
              <form id="fetchDataForm" class="mb-4">
                <div class="row">
                  <div class="col-3">
                    <label for="cert-type" class="form-label">Certificate Type</label>
                    <select name="cert-type" id="cert-type" class="form-select">
                      <option value="" hidden>Choose Certificate</option>
                      <option value="WASSCE">WASSCE/NECO</option>
                      <option value="SSSCE">SSSCE/GBCE</option>
                      <option value="Baccalaureate">BACCALAUREATE</option>
                      <option value="ALL">ALL</option>
                    </select>
                  </div>
                  <div class="col-3">
                    <label for="prog-type" class="form-label">Programme Category</label>
                    <select name="prog-type" id="prog-type" class="form-select">
                      <option value="" hidden>Choose Category</option>
                      <option value="first_prog">First Choice</option>
                      <option value="second_prog">Second Choice</option>
                    </select>
                  </div>
                  <div class="col-2">
                    <button type="submit" class="btn mb-4 btn-primary" style="margin-top: 30px;">Fetch Data</button>
                  </div>
                </div>
              </form>
              <div id="info-output"></div>
              <table class="table table-borderless datatable table-striped table-hover">
                <thead>
                  <tr class="table-dark">
                    <th scope="col">#</th>
                    <th scope="col" colspan="1">Full Name</th>
                    <th scope="col" colspan="1">Programme: (<span class="pro-choice">1<sup>st</sup></span>) Choice</th>
                    <th scope="col" colspan="4" style="text-align: center;">Core Subjects</th>
                    <th scope="col" colspan="4" style="text-align: center;">Elective Subjects</th>
                    <th scope="col" colspan="1" style="text-align: center;">Status</th>
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
                    <th scope="col"></th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
              <div class="mt-4" style="float:right">
                <button class="btn btn-primary" id="admit-all-bs">Admit All Qualified</button>
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

      //Use a default value when param is missing
      function getUrlParam(parameter, defaultvalue) {
        var urlparameter = defaultvalue;
        if (window.location.href.indexOf(parameter) > -1) {
          urlparameter = getUrlVars()[parameter];
        }
        return urlparameter;
      }

      if (getUrlVars()["status"] != "" || getUrlVars()["status"] != undefined) {
        if (getUrlVars()["exttrid"] != "" || getUrlVars()["exttrid"] != undefined) {}
      }

      $('#admit-all-bs').click(function() {
        data = {
          "cert-type": $("#cert-type").val(),
          "prog-type": $("#prog-type").val(),
        }

        $.ajax({
          type: "POST",
          url: "endpoint/admitAll",
          data: data,
          success: function(result) {
            console.log(result);
            if (result.success) fetchBroadsheet();

          },
          error: function(error) {
            console.log(error);
          }
        });
      });

      $("#awaiting-ds").change(function() {
        $("#upload-notification").text($(this).val()).show("slow");

        // Get the form element
        var form = $('form')[0];

        // Create a new FormData object
        var formData = new FormData(form);

        // Set up ajax request
        $.ajax({
          type: 'POST',
          url: "endpoint/extra-awaiting-data",
          data: formData,
          processData: false,
          contentType: false,
          success: function(result) {
            console.log(result);
            if (result.success) alert();
          },
          error: function() {
            alert('Error: Internal server error!');
          },
          ajaxStart: function() {
            $("#uploadBtn").prop("disabled", true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Uploading...');
          },
          ajaxStop: function() {
            $("#uploadBtn").prop("disabled", false).html('Upload');
          }
        });

      });

    });
  </script>

</body>

</html>