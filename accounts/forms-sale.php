<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
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

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row mx-auto">
            <!-- summary data buttons -->
            <button id="apps-total" class="btn btn-outline-primary col me-2 toggle-output">
              Total
              <span class="badge text-bg-secondary">
                <?= $expose->fetchTotalFormsSold()[0]["total"]; ?>
              </span>
            </button>

            <button id="apps-submitted" class="btn btn-outline-primary col me-2 toggle-output">
              Postgraduate
              <span class="badge text-bg-secondary">
                <?= $expose->fetchTotalPostgradsFormsSold()[0]["total"]; ?>
              </span>
            </button>

            <button id="apps-in-progress" class="btn btn-outline-primary col me-2 toggle-output">
              Undergraduate
              <span class="badge text-bg-secondary">
                <?= $expose->fetchTotalUdergradsFormsSold()[0]["total"]; ?>
              </span>
            </button>

            <button id="apps-admitted" class="btn btn-outline-primary col me-2 toggle-output">
              Short Courses
              <span class="badge text-bg-secondary">
                <?= $expose->fetchTotalShortCoursesFormsSold(true)[0]["total"]; ?>
              </span>
            </button>

          </div>

        </div><!-- End Left side columns -->

        <!-- Right side columns -->
        <!-- End Right side columns -->

    </section>

  </main><!-- End #main -->

  <?= require_once("inc/footer-section.php") ?>
  <script>
    $("dataTable-top").hide();
  </script>

</body>

</html>