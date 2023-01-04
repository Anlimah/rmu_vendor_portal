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

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Applications Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <a href="undergraduate.php">
                    <h5 class="card-title">Undergraduate</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                      </div>
                      <div class="ps-3">
                        <h6><?= $expose->fetchTotalApplications()[0]["total"]; ?></h6>
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
                  <a href="postgraduate.php">
                    <h5 class="card-title">Postgraduate</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-people"></i>
                      </div>
                      <div class="ps-3">
                        <h6><?= $expose->fetchTotalApplications()[0]["total"]; ?></h6>
                        <span class="text-muted small pt-2 ps-1">Applications</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div><!-- End Applications Card -->

            <!-- Admitted Students Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card text-success">
                <div class="card-body">
                  <a href="awaiting-results.php" style="text-decoration: none;">
                    <h5 class="card-title">Awaiting Results</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-check"></i>
                      </div>
                      <div class="ps-3">
                        <h6><?= $expose->fetchTotalAwaitingResults()[0]["total"]; ?></h6>
                        <span class="text-muted small pt-2 ps-1">awaiting results</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
            <!-- End Admitted Students Card -->

            <!-- Broadsheets Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card">
                <div class="card-body">
                  <a href="admit-applicants.php" style="text-decoration: none;">
                    <h5 class="card-title">Admit Applicants</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-files"></i>
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">Admit qualified applicants</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div><!-- End Broadsheets Card -->

            <!-- Forms Sales Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card text-danger">
                <div class="card-body">
                  <a href="forms-sale.php" style="text-decoration: none;">
                    <h5 class="card-title">Forms Sale</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-wallet2"></i>
                      </div>
                      <div class="ps-3">
                        <h6><?= $expose->fetchTotalApplications()[0]["total"]; ?></h6>
                        <span class="text-muted small pt-2 ps-1">Forms bought</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>

            <!-- Admitted Students Card -->
            <div class="col-xxl-3 col-md-3">
              <div class="card info-card text-success">
                <div class="card-body">
                  <a href="broadsheet.php" style="text-decoration: none;">
                    <h5 class="card-title">Broadsheet</h5>
                    <div class="d-flex align-items-center">
                      <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                        <i class="bi bi-person-check"></i>
                      </div>
                      <div class="ps-3">
                        <span class="text-muted small pt-2 ps-1">Download broadsheets</span>
                      </div>
                    </div>
                  </a>
                </div>
              </div>
            </div>
            <!-- End Admitted Students Card -->

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