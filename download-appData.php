<?php
session_start();

if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: login.php");
}

if (isset($_GET['logout'])  || strtolower($_SESSION["role"]) != "admissions") {
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
if (!isset($_GET['t']) || empty($_GET['t'])) header("Location: index.php");
if (!isset($_GET['q']) || empty($_GET['q'])) header("Location: applications.php?t={$_GET['t']}");
?>

<?php
require_once('bootstrap.php');
require_once('inc/page-data.php');

use Src\Controller\AdminController;
use Src\Controller\UsersController;

$admin = new AdminController();
$user = new UsersController();

$photo = $user->fetchApplicantPhoto($_GET['q']);
$personal = $user->fetchApplicantPersI($_GET['q']);
$appStatus = $user->getApplicationStatus($_GET['q']);

$pre_uni_rec = $user->fetchApplicantPreUni($_GET['q']);
$academic_BG = $user->fetchApplicantAcaB($_GET['q']);
$app_type = $user->getApplicationType($_GET['q']);

$personal_AB = $user->fetchApplicantProgI($_GET['q']);
$about_us = $user->fetchHowYouKnowUs($_GET['q']);

$uploads = $user->fetchUploadedDocs($_GET['q']);

$form_name = $admin->getFormTypeName($_GET["t"]);
$app_number = $admin->getApplicantAppNum($_GET["q"]);

$admin->updateApplicationStatus($_GET["q"]);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1.0" name="viewport">
    <meta name="author" content="Francis A. Anlimah">
    <meta name="email" content="francis.ano.anlimah@gmail.com">
    <meta name="website" content="https://linkedin.com/in/francis-anlimah">
    <title>Dashboard - Admissions</title>

    <!-- Favicons -->
    <link href="assets/img/rmu-logo.png" rel="icon">
    <link href="assets/img/rmu-logo.png" rel="apple-touch-icon">

    <!-- Google Fonts -->
    <!--<link href="https://fonts.gstatic.com" rel="preconnect">-->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

    <!-- Vendor CSS Files -->
    <!--<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-KK94CHFLLe+nY2dmCWGMq91rCGa5gtU4mk92HdvYe+M/SXH301p5ILy+dN9+nJOZ" crossorigin="anonymous">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">-->
    <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="assets/vendor/bootstrap-icons/bootstrap-icons.css">
    <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
    <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
    <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
    <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .btn-group-xs>.btn,
        .btn-xs {
            padding: 1px 5px;
            font-size: 12px;
            line-height: 1.5;
            border-radius: 3px;
        }

        input.transform-text,
        select.transform-text,
        textarea.transform-text {
            text-transform: uppercase !important;
        }
    </style>
    <script src="js/jquery-3.6.0.min.js"></script>
    <style>
        .arrow {
            display: inline-block;
            margin-left: 10px;
        }

        .edu-history {
            width: 100% !important;
            /*height: 120px !important;*/
            background-color: #fff !important;
            border: 1px solid #ccc !important;
            border-radius: 5px !important;
            display: flex !important;
            flex-direction: column !important;
        }

        .edu-history-header {
            width: 100% !important;
            height: 84px !important;
            background-color: #fff !important;
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
        }

        .edu-history-header-info {
            width: 80% !important;
            height: 100% !important;
            padding: 10px 20px !important;
        }

        .edu-history-control {
            height: 50px !important;
            background-color: #e6e6e6 !important;
            display: flex !important;
            flex-direction: row !important;
            justify-content: space-between !important;
            padding: 0 !important;
            margin: 0 !important;
        }

        .edu-history-footer {
            width: 100% !important;
            height: 100% !important;
            background-color: #ffffb3 !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: row !important;
            padding: 6px 20px !important;
        }

        .photo-display {
            width: 150px !important;
            height: 150px !important;
            min-width: 150px !important;
            min-height: 150px !important;
            /*background: red;*/
            border-radius: 5px;
            border: 1px solid #aaa;
            background: #f1f1f1;
            padding: 5px;
        }

        .photo-display>img {
            width: 100% !important;
        }

        .photo-display>img {
            width: 100% !important;
            height: 100% !important;
            border-radius: 5px;
        }
    </style>
</head>

<body>
    <section class=" section dashboard">

        <!-- programs summary view -->
        <div class="row">

            <!-- Recent Sales -->
            <div class="col-12">

                <div class="card recent-sales overflow-auto">

                    <div class="card-body" style="padding-top: 10px;">
                        <!--<h5 class="card-title">Applications</h5>-->

                        <div class="row">
                            <h3>Personal Information</h3>
                            <div style="display: flex;">
                                <div class="photo-display" style="margin-top: 5px; margin-right: 25px;">
                                    <img id="app-photo" src="<?= 'https://admissions.rmuictonline.com/apply/photos/' . $personal[0]["photo"] ?>" alt="">
                                </div>
                                <div style="display: flex; flex-direction: column">
                                    <div class="col">
                                        <h3>Personal Information</h3>
                                        <div>
                                            <p>
                                                <span><b>Name: </b> </span>
                                                <span><?= $personal[0]["first_name"] ?> <?= $personal[0]["middle_name"] ?> <?= $personal[0]["last_name"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Gender: </b> </span>
                                                <span><?= $personal[0]["gender"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Date of Birth: </b> </span>
                                                <span><?= $personal[0]["dob"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Marital Status:</b> </span>
                                                <span><?= $personal[0]["marital_status"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Nationality:</b> </span>
                                                <span><?= $personal[0]["nationality"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Country of residence: </b> </span>
                                                <span><?= $personal[0]["country_res"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Disabled?: </b> </span>
                                                <span><?= $personal[0]["disability"] ? "YES" : "NO" ?></span>
                                                <span> <?= " - " . $personal[0]["disability_descript"] ?> </span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>English Native?: </b> </span>
                                                <span><?= $personal[0]["english_native"] ? "YES" : "NO" ?></span>
                                                <span> - <?= $personal[0]["other_language"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Address Line 1: </b> </span>
                                                <span><?= $personal[0]["postal_addr"] ?> <?= $personal[0]["postal_town"] . ", " ?> <?= $personal[0]["postal_spr"] . " - " ?> <?= $personal[0]["postal_country"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Primary phone number: </b> </span>
                                                <span><?= $personal[0]["phone_no1_code"] ?> <?= $personal[0]["phone_no1"] ?></span>
                                            </p>
                                            <p>
                                                <span><b>Secondary phone number: </b> </span>
                                                <span><?= $personal[0]["phone_no2_code"] ?> <?= $personal[0]["phone_no2"] ?></span>
                                            </p>
                                            <p>
                                                <span><b>Email address: </b> </span>
                                                <span><?= $personal[0]["email_addr"] ?></span>
                                            </p>
                                        </div>
                                    </div>


                                    <div class="col" style="margin-top: 25px">
                                        <h3>Guardian/Parent Information</h3>
                                        <div>
                                            <p>
                                                <span><b>Name: </b> </span>
                                                <span><?= $personal[0]["p_first_name"] ?> <?= $personal[0]["p_last_name"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Occupation: </b> </span>
                                                <span><?= $personal[0]["p_occupation"] ?></span>
                                            </p>
                                        </div>
                                        <div>
                                            <p>
                                                <span><b>Phone number: </b> </span>
                                                <span><?= $personal[0]["p_phone_no_code"] ?> <?= $personal[0]["p_phone_no"] ?></span>
                                            </p>
                                            <p>
                                                <span><b>Email address: </b> </span>
                                                <span><?= $personal[0]["p_email_addr"] ?></span>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Education Background -->

                            <h3 style="margin-top: 270px;">Education Background</h3>
                            <div class="col mb-4">
                                <h5 style="font-size: 16px;" class="form-label mt-4"><b>List of schools</b></h5>
                                <div class="col">
                                    <?php
                                    if (!empty($academic_BG)) {
                                        foreach ($academic_BG as $edu_hist) {
                                    ?>
                                            <div class="mb-4 edu-history" id="<?= $edu_hist["s_number"] ?>">
                                                <div class="edu-history-header">
                                                    <div class="edu-history-header-info">
                                                        <p style="font-size: 16px; font-weight: 600;margin:0;padding:0">
                                                            <?= htmlspecialchars_decode(html_entity_decode(ucwords(strtolower($edu_hist["school_name"])), ENT_QUOTES), ENT_QUOTES); ?>
                                                            (<?= htmlspecialchars_decode(html_entity_decode(ucwords(strtolower($edu_hist["course_of_study"])), ENT_QUOTES), ENT_QUOTES); ?>)
                                                        </p>
                                                        <p style="color:#8c8c8c;margin:0;padding:0">
                                                            <?= ucwords(strtolower($edu_hist["month_started"])) . " " . ucwords(strtolower($edu_hist["year_started"])) . " - " ?>
                                                            <?= ucwords(strtolower($edu_hist["month_completed"])) . " " . ucwords(strtolower($edu_hist["year_completed"])) ?>
                                                        </p>
                                                    </div>
                                                    <div class="edu-history-control">
                                                        <button type="button" class="btn " name="edit-edu-btn" id="edit<?= $edu_hist["s_number"] ?>">
                                                            <span class="bi bi-caret-down-fill edit-edu-btn" style="font-size: 20px !important;"></span>
                                                        </button>
                                                        <button type="button" class="btn edit-edu-btn" name="edit-edu-btn" id="edit<?= $edu_hist["s_number"] ?>" style="display: none">
                                                            <span class="bi bi-caret-up-fill" style="font-size: 20px !important;"></span>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="edu-history-footer">
                                                    <table>
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row" style="width: 150px;">Country: </th>
                                                                <td><?= $edu_hist["country"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Region: </th>
                                                                <td><?= $edu_hist["region"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Certificate Type: </th>
                                                                <td><?= $edu_hist["cert_type"] ?></td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row">Awaiting Status: </th>
                                                                <td><?= $edu_hist["awaiting_result"] ? "YES" : "NO" ?></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                    <?php
                                        }
                                    }
                                    ?>
                                </div>
                            </div>

                        </div>

                        <!-- Programmes -->
                        <div class="col">
                            <h3>Programmes</h3>
                            <div class="certificates mb-4">
                                <?php
                                if (!empty($personal_AB)) {
                                ?>
                                    <div class="mt-4 mb-4" style="font-weight: 600;">
                                        <p>TERM APPLIED: <span><?= $personal_AB[0]["application_term"] ?></span></p>
                                        <p>STREAM APPLIED: <span><?= $personal_AB[0]["study_stream"] ?></span></p>
                                    </div>
                                    <table class="table table-striped">
                                        <thead class="table-dark">
                                            <tr>
                                                <th scope="col">CHOICE</th>
                                                <th scope="col">PROGRAMME</th>
                                                <th scope="col"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>1<sup>st</sup></td>
                                                <td><?= ucwords(strtoupper($personal_AB[0]["first_prog"])) ?></td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input app-prog-admit" style="cursor: pointer; float: right" type="radio" name="admit-prog" value="first_prog" data-prog="<?= $personal_AB[0]["first_prog"] ?>">
                                                    </div>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                <?php
                                }
                                ?>
                            </div>
                        </div>

                    </div>

                </div>
            </div><!-- End Recent Sales -->

        </div> <!-- programs summary view -->
        <!-- Right side columns -->
        <!-- End Right side columns -->

    </section>

    <script>
        window.print();
        window.close();
    </script>
</body>