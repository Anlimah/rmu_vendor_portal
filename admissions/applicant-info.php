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
            width: 90px !important;
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
            height: 36px !important;
            background-color: #ffffb3 !important;
            margin: 0 !important;
            padding: 0 !important;
            display: flex !important;
            flex-direction: row !important;
            padding: 6px 20px !important;
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

                        <div class="card-body" style="padding-top: 10px;">
                            <!--<h5 class="card-title">Applications</h5>-->

                            <div class="row">
                                <div class="col-6" style="border-right: 1px solid #ccc;">
                                    <div class="col">
                                        <div style="display: flex;">
                                            <div class="photo-display" style="margin-top: 5px; margin-right: 25px">
                                                <img id="app-photo" src="<?= $_SERVER["DOCUMENT_ROOT"] . '/rmu_admissions/apply/photos/1664974457.251.jpg' ?>" alt="">
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

                                    </div>
                                </div>

                                <div class="col-6">
                                    <!-- Education Background -->
                                    <div class="col">
                                        <h3>Education Background</h3>
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
                                                            </div>
                                                        </div>
                                                <?php
                                                    }
                                                }
                                                ?>
                                            </div>
                                        </div>

                                        <div class="col mb-4">
                                            <h5 style="font-size: 16px;" class="form-label mt-4"><b>List of documents</b></h5>
                                            <div class="certificates mb-4">
                                                <?php
                                                if (!empty($uploads)) {
                                                ?>
                                                    <table class="table table-striped">
                                                        <thead class="table-dark">
                                                            <tr>
                                                                <th scope="col">S/N</th>
                                                                <th scope="col">DOCUMENT TYPE</th>
                                                                <th scope="col"> </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php
                                                            $ind = 1;
                                                            foreach ($uploads as $cert) {
                                                            ?>
                                                                <tr>
                                                                    <th scope="row"><?= $ind ?></th>
                                                                    <td><?= ucwords(strtoupper($cert["type"])) ?></td>
                                                                    <td> <button type="button" style="cursor: pointer; float: right" class="btn btn-primary btn-sm delete-file" id="tran-delete-<?= $cert["id"] ?>" title="Open"><span class="bi bi-eye"></span></button></td>
                                                                </tr>
                                                            <?php
                                                                $ind += 1;
                                                            }
                                                            ?>
                                                        </tbody>
                                                    </table>
                                                <?php
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
                                                        <tr>
                                                            <td>2<sup>nd</sup></td>
                                                            <td><?= ucwords(strtoupper($personal_AB[0]["second_prog"])) ?></td>
                                                            <td>
                                                                <div class="form-check">
                                                                    <input class="form-check-input app-prog-admit" style="cursor: pointer; float: right" type="radio" name="admit-prog" value="second_prog" data-prog="<?= $personal_AB[0]["second_prog"] ?>">
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

                                    <div class="col" style="margin-top:100px">
                                        <form method="post" id="admit-applicant-form">
                                            <input type="hidden" name="app-prog" id="app-prog">
                                            <input type="hidden" name="app-login" id="app-login" value="<?= $personal_AB[0]["app_login"] ?>">
                                            <button class="btn btn-success btn-lg" style="width: 100%;" type="submit">Admit</button>
                                        </form>
                                        <form method="post" id="decline-applicant-form">
                                            <input type="hidden" name="app-login" value="<?= $personal_AB[0]["app_login"] ?>">
                                            <button class="btn btn-danger btn-lg" style="width: 100%;" type="submit">Decline</button>
                                        </form>
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


            $(".app-prog-admit").on("click", function() {
                let prog = $(this).val();
                $("#app-prog").val(prog);
            })

            $("#admit-applicant-form").on("submit", function(e) {
                e.preventDefault();
                var c = confirm("Are you sure you want to admit this applicant?");
                if (c) {
                    $.ajax({
                        type: "POST",
                        url: "../endpoint/admit-individual-applicant",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(result) {
                            console.log(result);
                            alert(result.message);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                }
            });

            $("#decline-applicant-form").on("submit", function(e) {
                e.preventDefault();
                var c = confirm("Are you sure you want to admit this applicant?");
                if (c) {
                    $.ajax({
                        type: "POST",
                        url: "../endpoint/decline-individual-applicant",
                        data: new FormData(this),
                        contentType: false,
                        cache: false,
                        processData: false,
                        success: function(result) {
                            console.log(result);
                            alert(result.message);
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
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