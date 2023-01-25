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
            <h1>Settings</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item active">Settings</li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class="section dashboard">

            <!-- Dashboard view -->
            <div class="row">

                <!-- Left side columns -->
                <div class="col-lg-12">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="forms-tab" data-bs-toggle="tab" data-bs-target="#forms-tab-pane" type="button" role="tab" aria-controls="forms-tab-pane" aria-selected="true">Forms</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="vendors-tab" data-bs-toggle="tab" data-bs-target="#vendors-tab-pane" type="button" role="tab" aria-controls="vendors-tab-pane" aria-selected="false">Vendors</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="programmes-tab" data-bs-toggle="tab" data-bs-target="#programmes-tab-pane" type="button" role="tab" aria-controls="programmes-tab-pane" aria-selected="false">Programmes</button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="admission-tab" data-bs-toggle="tab" data-bs-target="#admission-tab-pane" type="button" role="tab" aria-controls="admission-tab-pane" aria-selected="false">Admission Period</button>
                        </li>
                    </ul>

                    <div class="tab-content" id="myTabContent">
                        <?php require_once("inc/settings/forms-pane.php"); ?>
                        <?php require_once("inc/settings/vendors-pane.php"); ?>
                        <?php require_once("inc/settings/programmes-pane.php"); ?>
                        <?php require_once("inc/settings/admissions-pane.php"); ?>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <?= require_once("inc/footer-section.php") ?>

    <script>
        $(document).ready(function() {});
    </script>

</body>

</html>