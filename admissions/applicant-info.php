<?php
session_start();

if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: ../login.php");
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

    header('Location: ../login.php');
}
if (!isset($_GET['t']) || empty($_GET['t'])) header("Location: index.php");
if (!isset($_GET['q']) || empty($_GET['q'])) header("Location: applications.php?t={$_GET['t']}");
?>

<?php
require_once('../bootstrap.php');
require_once('../inc/page-data.php');

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
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= require_once("../inc/head.php") ?>
    <style>
        .arrow {
            display: inline-block;
            margin-left: 10px;
        }
    </style>
</head>

<body>
    <?= require_once("../inc/header.php") ?>

    <?= require_once("../inc/sidebar.php") ?>

    <main id="main" class="main">

        <div class="pagetitle">
            <h1>Applications</h1>
            <nav>
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="applications.php?t=<?= $_GET["t"] ?>"><?= $form_name[0]["name"] ?></a></li>
                    <li class="breadcrumb-item active"><?= $app_number[0]["app_number"] ?></li>
                </ol>
            </nav>
        </div><!-- End Page Title -->

        <section class=" section dashboard">

            <!-- programs summary view -->
            <div class="row">

                <!-- Recent Sales -->
                <div class="col-12">

                    <div class="card recent-sales overflow-auto">

                        <div class="card-body">
                            <h5 class="card-title">Applications</h5>
                            <div class="row">
                                <div class="col-2">
                                    <div class="photo-display">
                                        <img id="app-photo" src="<?= $_SERVER["DOCUMENT_ROOT"] . '/rmu_admissions/apply/photos/1664974457.251.jpg' ?>" alt="">
                                    </div>
                                </div>

                                <div class="col-10 bg-info">
                                    <div class="row">
                                        <div class="col-6 bg-success">
                                            <h1>Personal Information</h1>
                                            <span>Name:</span>
                                        </div>
                                        <div class="col-6 bg-warning">
                                            <h1>Guardian/Parent Information</h1>
                                            Guardian/Parent Info
                                        </div>

                                    </div>
                                </div>

                            </div>

                        </div>

                    </div>
                </div><!-- End Recent Sales -->

            </div> <!-- programs summary view -->
            <!-- Right side columns -->
            <!-- End Right side columns -->

        </section>

    </main><!-- End #main -->

    <?= require_once("../inc/footer-section.php") ?>

    <script>
        // when 
        $(document).ready(function() {
            var summary_selected = "";
            // when a summary data button is clicked
            $(".toggle-output").click(function() {
                $('.toggle-output').css('border-bottom', 'none');
                $(this).css('border-bottom', '3px solid #000');

                // Remove arrow from all buttons
                $(".arrow").remove();
                $(".form-select option:selected").attr("selected", false);
                $(".form-select option[value='All']").attr('selected', true);

                // Add arrow to selected button
                $(this).append("<span class='arrow'>&#x25BC;</span>");

                summary_selected = $(this).attr("id");
                data = {
                    action: summary_selected,
                    form_t: getUrlVars()["t"]
                };

                $.ajax({
                    type: "POST",
                    url: "../endpoint/apps-data",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                let status = value.declaration == 1 ? '<span class="badge text-bg-success">Submitted</span>' : '<span class="badge text-bg-danger">In Progress</span>';
                                $("tbody").append(
                                    '<tr>' +
                                    '<th scope="row"><a href="javascript:void()">' + (index + 1) + '</a></th>' +
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
                e.preventDefault();

                data = {
                    "action": summary_selected,
                    "country": $("#country").val(),
                    "type": getUrlVars()["t"],
                    "program": $("#program").val(),
                }

                var id = this.id

                $.ajax({
                    type: "POST",
                    url: "../endpoint/applicants",
                    data: data,
                    success: function(result) {
                        console.log(result);

                        if (result.success) {
                            $("tbody").html('');
                            $.each(result.message, function(index, value) {
                                let status = value.declaration == 1 ? '<span class="badge text-bg-success">Submitted</span>' : '<span class="badge text-bg-danger">In Progress</span>';
                                $("tbody").append(
                                    '<tr>' +
                                    '<th scope="row"><a href="javascript:void()">' + (index + 1) + '</a></th>' +
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

                        if (id == "type") {
                            $.ajax({
                                type: "GET",
                                url: "../endpoint/programs",
                                data: {
                                    "type": getUrlVars()["t"],
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
                        "type": getUrlVars()["t"],
                        "program": $("#program").val(),
                    }
                    window.open("../export-excel.php?w=sdjgskfsd&a=hoh&c=jgkg&t=hjgkj&p=jgksjgks", "_blank");
                }
            });

            $(".download-pdf").click(function() {
                if (summary_selected !== "") {
                    data = {
                        "action": summary_selected,
                        "country": $("#country").val(),
                        "type": getUrlVars()["t"],
                        "program": $("#program").val(),
                    }
                    window.open("../download-pdf.php?w=apps&t=" + getUrlVars()["t"] + "&a=" + data["action"] + "&c=" + data["country"] + "&t=" + data["type"] + "&p=" + data["program"], "_blank");
                }
            });


        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@2.1.7/dist/loadingoverlay.min.js"></script>
    <script>
        $(document).ready(function() {
            $(document).on({
                ajaxStart: function() {
                    // Show full page LoadingOverlay
                    $.LoadingOverlay("show");
                },
                ajaxStop: function() {
                    // Hide it after 3 seconds
                    $.LoadingOverlay("hide");
                }
            });
        });
    </script>
</body>

</html>