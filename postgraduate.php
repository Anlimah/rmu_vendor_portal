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
              <span class="icon export-excel" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Export Excel">
                <img src="assets/img/icons8-microsoft-excel-2019-48.png" alt="" style="width: 24px;">
              </span>
              <span class="icon download-pdf" data-bs-toggle="tooltip" data-bs-placement="bottom" data-bs-title="Download PDF">
                <img src="assets/img/icons8-pdf-48.png" alt="" style="width: 24px;">
              </span>
            </div>

            <div class="card-body">
              <h5 class="card-title">Applicantions</h5>

              <div class="row mx-auto">
                <!-- summary data buttons -->
                <button id="apps-total" class="btn btn-outline-primary col me-2 toggle-output">
                  Total
                  <span class="badge text-bg-secondary">
                    <?= $expose->fetchTotalApplications("Postgraduate")[0]["total"]; ?>
                  </span>
                </button>

                <button id="apps-submitted" class="btn btn-outline-primary col me-2 toggle-output">
                  Submitted
                  <span class="badge text-bg-secondary">
                    <?= $expose->fetchTotalSubmittedOrUnsubmittedApps()[0]["total"]; ?>
                  </span>
                </button>

                <button id="apps-in-progress" class="btn btn-outline-primary col me-2 toggle-output">
                  In Progress
                  <span class="badge text-bg-secondary">
                    <?= $expose->fetchTotalSubmittedOrUnsubmittedApps(false)[0]["total"]; ?>
                  </span>
                </button>

                <button id="apps-admitted" class="btn btn-outline-primary col me-2 toggle-output">
                  Admitted
                  <span class="badge text-bg-secondary">
                    <?= $expose->fetchTotalAdmittedOrUnadmittedApplicants(true)[0]["total"]; ?>
                  </span>
                </button>

                <button id="apps-unadmitted" class="btn btn-outline-primary col me-2 toggle-output">
                  Unqualified
                  <span class="badge text-bg-secondary">
                    <?= $expose->fetchTotalAdmittedOrUnadmittedApplicants(false)[0]["total"]; ?>
                  </span>
                </button>

              </div>
              <div class="collapse" id="toggle-output">
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
                      <th scope="col">Name</th>
                      <th scope="col">Country</th>
                      <th scope="col">Application Type</th>
                      <th scope="col">Programme (1<sup>st</sup> Choice)</th>
                      <th scope="col">Status</th>
                      <th scope="col">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              </div>

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
      var summary_selected = "";
      // when a summary data button is clicked
      $(".toggle-output").click(function() {
        summary_selected = $(this).attr("id");
        alert(summary_selected)
        $.ajax({
          type: "POST",
          url: "endpoint/apps-data",
          data: {
            action: summary_selected
          },
          success: function(result) {
            console.log(result);

            if (result.success) {
              $("tbody").html('');
              $.each(result.message, function(index, value) {
                let status = value.declaration == 1 ? '<span class="badge text-bg-success">Submitted</span>' : '<span class="badge text-bg-danger">In Progress</span>';
                $("tbody").append(
                  '<tr>' +
                  '<th scope="row"><a href="javascript:void()">' + value.id + '</a></th>' +
                  '<td>' + value.fullname + '</td>' +
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
          },
          error: function(error) {
            console.log(error);
          }
        });

        if ($("#toggle-output").is(":visible") === false) $("#toggle-output").slideToggle();
      });

      // when 
      $(".form-select").change("blur", function(e) {
        alert(summary_selected)
        e.preventDefault();
        data = {
          "action": summary_selected,
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

      $(".export-excel").click(function() {
        if (summary_selected !== "") {
          data = {
            "action": summary_selected,
            "country": $("#country").val(),
            "type": $("#type").val(),
            "program": $("#program").val(),
          }
          window.open("export-excel.php?w=sdjgskfsd&a=hoh&c=jgkg&t=hjgkj&p=jgksjgks", "_blank");
        }
      });

      $(".download-pdf").click(function() {
        if (summary_selected !== "") {
          data = {
            "action": summary_selected,
            "country": $("#country").val(),
            "type": $("#type").val(),
            "program": $("#program").val(),
          }
          window.open("download-pdf.php", "_blank");
        }
      });


    });
  </script>

</body>

</html>