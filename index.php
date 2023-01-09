<?php
require_once('bootstrap.php');

use Src\Controller\AdminController;

$admin = new AdminController();
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
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Applications Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <a href="applications.php">
                    <h5 class="card-title">Applications</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                      </div>
                      <div class="ps-3">
                        <h6><?= $admin->fetchTotalApplications()[0]["total"]; ?></h6>
                        <span class="text-muted small pt-2 ps-1">Applications</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div><!-- End Applications Card -->

            <!-- Applications Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <a href="https://forms.rmuictonline.com/buy-vendor/">
                    <h5 class="card-title">Sell Form</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <img src="./assets/img/icons8-sell-48.png" style="width: 48px;" alt="">
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">forms</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div><!-- End Applications Card -->

            <!-- Applications Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <a href="forms-sale.php">
                    <h5 class="card-title">Form Sales Stats</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <img src="./assets/img/icons8-stocks-growth-96.png" style="width: 48px;" alt="">
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">Statistics</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div><!-- End Applications Card -->

            <!-- Applications Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <a href="general-settings.php">
                    <h5 class="card-title">Settings</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <img src="./assets/img/icons8-services-96.png" style="width: 48px;" alt="">
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">Statistics</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div><!-- End Applications Card -->

          </div>
        </div><!-- Forms Sales Card  -->

      </div><!-- End Left side columns -->

      <!-- Right side columns -->
      <!-- End Right side columns -->

    </section>

  </main><!-- End #main -->

  <?= require_once("inc/footer-section.php") ?>
  <script src="js/jquery-3.6.0.min.js"></script>
  <script>
    $("dataTable-top").hide();
  </script>

</body>

</html>