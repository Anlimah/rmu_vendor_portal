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
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Recent Sales -->
        <div class="col-12">

          <div class="card recent-sales overflow-auto">

            <div class="filter">
              <a class="icon" href="javascript:void()" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download PDF"><i class="bi bi-download"></i></a>
            </div>

            <div class="card-body">
              <h5 class="card-title">Applicantions</h5>

              <div class="row">

                <!-- Broadsheets Card -->
                <div class="col-xxl-3 col-md-3">
                  <div class="card info-card" style="border: 1px solid #999; padding: 0 !important">
                    <div class="card-body row" style="padding: 0px;">
                      <div class="col-md-8" style="padding: 0 !important; margin: 0 !important; display: flex; flex-direction: row; align-items:baseline">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-people-fill" style="font-size: 25px;"></i>
                        </div>
                        <h5 class="card-title">Total</h5>
                        <div class="d-flex align-items-center">
                          <div class="ps-3">
                            <h6><?= $expose->fetchTotalApplications()[0]["total"]; ?></h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- End Broadsheets Card -->

                <!-- Broadsheets Card -->
                <div class="col-xxl-3 col-md-3">
                  <div class="card info-card" style="border: 1px solid #999; padding: 0 !important">
                    <div class="card-body row" style="padding: 0px;">
                      <div class="col-md-8" style="padding: 0 !important; margin: 0 !important; display: flex; flex-direction: row; align-items:baseline">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-files" style="font-size: 25px;"></i>
                        </div>
                        <h5 class="card-title col">Submitted</h5>
                        <div class="d-flex align-items-center">
                          <div class="ps-3">
                            <h6><?= $expose->fetchTotalSubmittedOrUnsubmittedApps()[0]["total"]; ?></h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- End Broadsheets Card -->

                <!-- Broadsheets Card -->
                <div class="col-xxl-3 col-md-3">
                  <div class="card info-card" style="border: 1px solid #999; padding: 0 !important">
                    <div class="card-body row" style="padding: 0px;">
                      <div class="col-md-8" style="padding: 0 !important; margin: 0 !important; display: flex; flex-direction: row; align-items:baseline">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-lines-fill" style="font-size: 25px;"></i>
                        </div>
                        <h5 class="card-title">Progress</h5>
                        <div class="ps-3">
                          <h6><?= $expose->fetchTotalSubmittedOrUnsubmittedApps(false)[0]["total"]; ?></h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div><!-- End Broadsheets Card -->

                <!-- Broadsheets Card -->
                <div class="col-xxl-3 col-md-3">
                  <div class="card info-card" style="border: 1px solid #999; padding: 0 !important">
                    <div class="card-body row" style="padding: 0px;">
                      <div class="col-md-8" style="padding: 0 !important; margin: 0 !important; display: flex; flex-direction: row; align-items:baseline">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-check" style="font-size: 25px;"></i>
                        </div>
                        <h5 class="card-title col">Admitted</h5>
                        <div class="ps-3">
                          <h6><?= $expose->fetchTotalAdmittedApplicants()[0]["total"]; ?></h6>
                        </div>
                      </div>

                    </div>
                  </div>
                </div><!-- End Broadsheets Card -->

              </div>

              <hr class="mb-4">

              <form action="" class="mb-4 mt-4" id="form-filter">
                <div class="row">
                  <div class="col-4">
                    <label for="country" class="form-label">Country</label>
                    <select name="country" id="country" class="form-select">
                      <option value="All" selected>All</option>
                      <option value="Cameroun">Cameroun</option>
                      <option value="Ghana">Ghana</option>
                      <option value="Guinea">Guinea</option>
                      <option value="Liberia">Liberia</option>
                      <option value="Sierra Leone">Sierra Leone</option>
                      <option value="Others">Others</option>
                    </select>
                  </div>
                  <div class="col-4">
                    <label for="type" class="form-label">Application Type</label>
                    <select name="type" id="type" class="form-select">
                      <option value="All" selected>All</option>
                      <?php
                      $data = $expose->getFormTypes();
                      foreach ($data as $ft) {
                      ?>
                        <option value="<?= $ft['id'] ?>"><?= $ft['name'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                  <div class="col-4">
                    <label for="program" class="form-label">Programs</label>
                    <select name="program" id="program" class="form-select">
                      <option value="All">All</option>
                      <?php
                      $data = $expose->fetchPrograms(0);
                      foreach ($data as $ft) {
                      ?>
                        <option value="<?= $ft['id'] ?>"><?= $ft['name'] ?></option>
                      <?php
                      }
                      ?>
                    </select>
                  </div>
                </div>
              </form>
              <div id="info-output"></div>
              <table class="table table-borderless datatable table-striped table-hover">
                <thead class="table-dark">
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">First & Last Name</th>
                    <th scope="col">Country</th>
                    <th scope="col">Application Type</th>
                    <th scope="col">Programme (1<sup>st</sup> Choice)</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                  $data = $expose->fetchAllApplicants();
                  foreach ($data as $ft) {
                    $status = $ft["declaration"] == 1 ? '<span class="badge text-bg-success">Submitted</span>' : '<span class="badge text-bg-danger">In Progress</span>';
                  ?>
                    <tr>
                      <th scope="row"><?= $ft['id'] ?></th>
                      <td><?= $ft["first_name"] . " " . $ft["last_name"] ?></td>
                      <td><?= $ft["nationality"] ?></td>
                      <td><?= $ft["app_type"] ?></td>
                      <td><?= $ft["first_prog"] ?></td>
                      <td><?= $status ?></td>
                      <td><b><a href="applicant-info.php?q=' + value.id + '">Open</a></b></td>
                    </tr>
                  <?php
                  }
                  ?>
                </tbody>
              </table>

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

      $(".form-select").change("blur", function(e) {
        e.preventDefault();
        data = {
          "country": $("#country").val(),
          "type": $("#type").val(),
          "program": $("#program").val(),
        }

        var id = this.id

        $.ajax({
          type: "POST",
          url: "endpoint/applicants",
          data: data,
          success: function(result) {
            console.log(result);

            if (result.success) {
              $("tbody").html('');
              $.each(result.message, function(index, value) {
                let status = value.declaration == 1 ? '<span class="badge text-bg-success">Submitted</span>' : '<span class="badge text-bg-danger">In Progress</span>';
                $("tbody").append(
                  '<tr>' +
                  '<th scope="row"><a href="javascript:void()">' + value.id + '</a></th>' +
                  '<td>' + value.first_name + ' ' + value.last_name + '</td>' +
                  '<td>' + value.nationality + '</td>' +
                  '<td>' + value.app_type + '</td>' +
                  '<td>' + value.first_prog + '</td>' +
                  '<td>' + status + '</td>' +
                  '<td><b><a href="applicant-info.php?q=' + value.id + '">Open</a></b></td>' +
                  '</tr>');
              });

            } else {
              $("tbody").html('');
              $("#info-output").html(
                '<div class="alert alert-info alert-dismissible fade show" role="alert">' +
                '<i class="bi bi-info-circle me-1"></i>' + result.message +
                '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>' +
                '</div>'
              );
            }

            if (id == "type") {
              $.ajax({
                type: "GET",
                url: "endpoint/programs",
                data: {
                  "type": $("#type").val(),
                },
                success: function(result) {
                  console.log(result);
                  if (result.success) {
                    $("#program").html('<option value="All">All</option>');
                    $.each(result.message, function(index, value) {
                      $("#program").append('<option value="' + value.name + '">' + value.name + '</option>');
                    });
                  }
                },
                error: function(error) {
                  console.log(error);
                }
              });
            }

          },
          error: function(error) {
            console.log(error);
          }
        });
      });

      $(".printer").click(function() {
        let c = "c=" + $("#country").val();
        let t = "&t=" + $("#type").val();
        let p = "&p=" + $("#program").val();
        window.open("print-document.php?" + c + t + p, "_blank");
      });

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


    });
  </script>

</body>

</html>