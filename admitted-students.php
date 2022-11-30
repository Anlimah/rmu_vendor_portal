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
              <a class="icon" href="#" data-bs-toggle="dropdown" title="Filter List"><i class="bi bi-three-dots"></i></a>
              <a class="icon printer" href="#" title="Print"><i class="bi bi-printer-fill"></i></a>
              <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                <li class="dropdown-header text-start">
                  <h6>Filter</h6>
                </li>

                <li><a class="dropdown-item" href="#">Today</a></li>
                <li><a class="dropdown-item" href="#">This Month</a></li>
                <li><a class="dropdown-item" href="#">This Year</a></li>
              </ul>
            </div>

            <div class="card-body">
              <h5 class="card-title">Applicants <span>| Today</span></h5>

              <form action="" class="mb-4">
                <div class="row">
                  <div class="col-4">
                    <label for="country" class="form-label">Country</label>
                    <select name="country" id="country" class="form-select">
                      <option value="All">All</option>
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
                      <option value="All">All</option>
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
                    </select>
                  </div>
                </div>
              </form>
              <div id="info-output"></div>
              <table class="table table-borderless datatable table-striped table-hover">
                <thead>
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
                    $status = $ft["declaration"] == 1 ? '<span class="text-success">Submitted</span>' : '<span class="text-danger">In Progress</span>';
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
                let status = value.declaration == 1 ? '<span class="text-success">Submitted</span>' : '<span class="text-danger">In Progress</span>';
                $("tbody").append(
                  '<tr>' +
                  '<th scope="row"><a href="#">' + value.id + '</a></th>' +
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