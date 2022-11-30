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
            <div class="col-xxl-4 col-md-4">
              <div class="card info-card sales-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Week</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Applications <span>| Today</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <div class="ps-3">
                      <a href="applicants.php">
                        <h6>145</h6>
                        <span class="text-muted small pt-2 ps-1">Applicants</span>
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Applications Card -->

            <!-- Broadsheets Card -->
            <div class="col-xxl-4 col-md-4 ">
              <div class="card info-card">

                <div class="card-body">
                  <h5 class="card-title">Broadsheets</h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-files"></i>
                    </div>
                    <div class="ps-3">
                      <a href="broadsheets.php" style="text-decoration: none;">
                        <span class="text-muted small pt-2 ps-1">Broadsheets</span>
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Broadsheets Card -->

            <!-- Admitted Students Card -->
            <div class="col-xxl-4 col-md-4 ">
              <div class="card info-card text-success">

                <div class="card-body">
                  <h5 class="card-title">Admit Applicants </h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-person-check"></i>
                    </div>
                    <div class="ps-3">
                      <a href="admitted-students.php" style="text-decoration: none;">
                        <h6>3</h6>
                        <span class="text-muted small pt-2 ps-1">Qualified</span>
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Admitted Students Card -->

            <!-- Admitted Students Card -->
            <div class="col-xxl-4 col-md-4 ">
              <div class="card info-card text-primary">

                <div class="card-body">
                  <h5 class="card-title">Sell Forms </h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cash-coin"></i>
                    </div>
                    <div class="ps-3">
                      <a href="admitted-students.php" style="text-decoration: none;">
                        <h6></h6>
                        <span class="text-muted small pt-2 ps-1">Sell a form</span>
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Admitted Students Card -->

            <!-- Admitted Students Card -->
            <div class="col-xxl-4 col-md-4 ">
              <div class="card info-card text-danger">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Week</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Forms Sales <span>| Today</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-wallet2"></i>
                    </div>
                    <div class="ps-3">
                      <a href="admitted-students.php" style="text-decoration: none;">
                        <h6>145</h6>
                        <span class="text-muted small pt-2 ps-1">Forms bought</span>
                      </a>
                    </div>
                  </div>
                </div>

              </div>
            </div><!-- End Admitted Students Card -->

          </div>
        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <!-- End Right side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <?= require_once("inc/footer-section.php") ?>
  <script src="js/jquery-3.6.0.min.js"></script>
  <script>
    $("dataTable-top").hide();
  </script>

</body>

</html>