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

                        <!--Forms Pane-->
                        <div class="tab-pane fade show active" id="forms-tab-pane" role="tabpanel" aria-labelledby="forms-tab" tabindex="0">
                            <div class="container mt-4">
                                <div class="row">
                                    <div class="col-lg-1">
                                    </div>
                                    <div class="col-lg-5">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col">Name</th>
                                                    <th scope="col">Price ($)</th>
                                                    <th scope="col"></th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $forms = $admin->fetchAllFormDetails();
                                                foreach ($forms as $form) {
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?= $form["id"] ?></th>
                                                        <td><?= $form["name"] ?></td>
                                                        <td><?= $form["amount"] ?></td>
                                                        <td id="<?= "edit-" . $form["id"] ?>" class="edit-form"><span style="cursor:pointer;" class="bi bi-pencil-square text-primary" title="Edit <?= $form["name"] ?>"></span></td>
                                                        <td id="<?= "delete-" . $form["id"] ?>" class="delete-form"><span style="cursor:pointer;" class="bi bi-trash text-danger" title="Delete <?= $form["name"] ?>"></span></td>
                                                    </tr>
                                                <?php
                                                }
                                                ?>
                                                <tr></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-2">
                                    </div>
                                    <div class="col-lg-3">
                                        <form id="formTypeForm" action="#" method="post" class="">
                                            <div class="card">
                                                <h5 class="card-header">Add Form</h5>
                                                <div class="card-body">
                                                    <!--<div class="mb-2">
                                                        <label for="form-name">Name/Title</label>
                                                        <input type="text" class="form-control form-control-sm" name="form-name" id="form-name">
                                                    </div>-->
                                                    <div class="mb-2">
                                                        <label for="form-name">Form Name</label>
                                                        <div style="display:flex; flex-direction:row; justify-content:baseline; align-items:baseline;">
                                                            <select name="form-type" id="form-type" class="form-select form-select-sm">
                                                                <?php
                                                                $data = $admin->fetchAvailableformTypes();
                                                                foreach ($data as $ft) {
                                                                ?>
                                                                    <option value="<?= $ft['id'] ?>"><?= $ft['name'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <span class="bi bi-plus-circle-fill text-success" style="margin-inline-start: 5px;" data-bs-toggle="modal" data-bs-target="#addFormType"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="form-price">Price</label>
                                                        <input type="text" class="form-control form-control-sm" name="form-price" id="form-price" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Add form type modal form-->
                                        <div class="modal fade" id="addFormType" tabindex="-1" aria-labelledby="addFormTypeLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Type</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="formTypeForm" action="#" method="post" class="">
                                                            <div class="card">
                                                                <h5 class="card-header">Add Form</h5>
                                                                <div class="card-body">
                                                                    <div class="mb-2">
                                                                        <label for="form-name">Action</label>
                                                                        <div style="display:flex; flex-direction:row; justify-content:baseline; align-items:baseline;">
                                                                            <select name="form-type" id="form-type" class="form-select form-select-sm">
                                                                                <option value="add">Add</option>
                                                                                <option value="Update">Update</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="form-price">Form Name</label>
                                                                        <input type="text" class="form-control form-control-sm" name="form-price" id="form-price" placeholder="0.00">
                                                                    </div>
                                                                    <div>
                                                                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary">Understood</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="col-lg-1">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Vendors Pane-->
                        <div class="tab-pane fade" id="vendors-tab-pane" role="tabpanel" aria-labelledby="vendors-tab" tabindex="0">
                            <div class="container mt-4">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" style="width: 250px;">Vendor Name</th>
                                                    <th scope="col">Email Address</th>
                                                    <th scope="col">Country Name</th>
                                                    <th scope="col">Phone Number</th>
                                                    <th scope="col"></th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $vendors = $admin->fetchAllVendorDetails();
                                                $i = 1;
                                                foreach ($vendors as $vendor) {
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?= $i ?></th>
                                                        <td><?= $vendor["vendor_name"] ?></td>
                                                        <td><?= $vendor["email_address"] ?></td>
                                                        <td><?= $vendor["country_name"] ?></td>
                                                        <td><?= $vendor["country_code"] . $vendor["phone_number"] ?></td>
                                                        <td id="<?= "edit-" . $vendor["id"] ?>" class="edit-form"><span style="cursor:pointer;" class="bi bi-pencil-square text-primary" title="Edit <?= $vendor["vendor_name"] ?>"></span></td>
                                                        <td id="<?= "delete-" . $vendor["id"] ?>" class="delete-form"><span style="cursor:pointer;" class="bi bi-trash text-danger" title="Delete <?= $vendor["vendor_name"] ?>"></span></td>
                                                    </tr>
                                                <?php
                                                    $i++;
                                                }
                                                ?>
                                                <tr></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-1">
                                    </div>
                                    <div class="col-lg-3">
                                        <form id="formTypeForm" action="#" method="post" class="">
                                            <div class="card">
                                                <h5 class="card-header">Add Form</h5>
                                                <div class="card-body">
                                                    <!--<div class="mb-2">
                                                        <label for="form-name">Name/Title</label>
                                                        <input type="text" class="form-control form-control-sm" name="form-name" id="form-name">
                                                    </div>-->
                                                    <div class="mb-2">
                                                        <label for="form-name">Form Name</label>
                                                        <div style="display:flex; flex-direction:row; justify-content:baseline; align-items:baseline;">
                                                            <select name="form-type" id="form-type" class="form-select form-select-sm">
                                                                <?php
                                                                $data = $admin->fetchAvailableformTypes();
                                                                foreach ($data as $ft) {
                                                                ?>
                                                                    <option value="<?= $ft['id'] ?>"><?= $ft['name'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <span class="bi bi-plus-circle-fill text-success" style="margin-inline-start: 5px;" data-bs-toggle="modal" data-bs-target="#addFormType"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="form-price">Price</label>
                                                        <input type="text" class="form-control form-control-sm" name="form-price" id="form-price" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Add form type modal form-->
                                        <div class="modal fade" id="addFormType" tabindex="-1" aria-labelledby="addFormTypeLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Type</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="formTypeForm" action="#" method="post" class="">
                                                            <div class="card">
                                                                <h5 class="card-header">Add Form</h5>
                                                                <div class="card-body">
                                                                    <div class="mb-2">
                                                                        <label for="form-name">Action</label>
                                                                        <div style="display:flex; flex-direction:row; justify-content:baseline; align-items:baseline;">
                                                                            <select name="form-type" id="form-type" class="form-select form-select-sm">
                                                                                <option value="add">Add</option>
                                                                                <option value="Update">Update</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="form-price">Form Name</label>
                                                                        <input type="text" class="form-control form-control-sm" name="form-price" id="form-price" placeholder="0.00">
                                                                    </div>
                                                                    <div>
                                                                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary">Understood</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Programmes Pane-->
                        <div class="tab-pane fade" id="programmes-tab-pane" role="tabpanel" aria-labelledby="programmes-tab" tabindex="0">
                            <div class="container mt-4">
                                <div class="row">
                                    <div class="col-lg-8">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th scope="col">#</th>
                                                    <th scope="col" style="width: 250px;">Program Name</th>
                                                    <th scope="col">Type</th>
                                                    <th scope="col">Weekend</th>
                                                    <th scope="col">Group</th>
                                                    <th scope="col"></th>
                                                    <th scope="col"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $programmes = $admin->fetchAllPrograms();
                                                $i = 1;
                                                foreach ($programmes as $prog) {
                                                ?>
                                                    <tr>
                                                        <th scope="row"><?= $i ?></th>
                                                        <td><?= $prog["name"] ?></td>
                                                        <td><?= $prog["type"] ?></td>
                                                        <td><?= $prog["weekend"] ? '<span class="bi bi-check-lg text-success"></span>' : '<span class="bi bi-x-lg text-danger"></span>' ?></td>
                                                        <td><?= $prog["group"] ?></td>
                                                        <td id="<?= "edit-" . $prog["id"] ?>" class="edit-form"><span style="cursor:pointer;" class="bi bi-pencil-square text-primary" title="Edit <?= $prog["name"] ?>"></span></td>
                                                        <td id="<?= "delete-" . $prog["id"] ?>" class="delete-form"><span style="cursor:pointer;" class="bi bi-trash text-danger" title="Delete <?= $prog["name"] ?>"></span></td>
                                                    </tr>
                                                <?php
                                                    $i++;
                                                }
                                                ?>
                                                <tr></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-lg-1">
                                    </div>
                                    <div class="col-lg-3">
                                        <form id="formTypeForm" action="#" method="post" class="">
                                            <div class="card">
                                                <h5 class="card-header">Add Form</h5>
                                                <div class="card-body">
                                                    <!--<div class="mb-2">
                                                        <label for="form-name">Name/Title</label>
                                                        <input type="text" class="form-control form-control-sm" name="form-name" id="form-name">
                                                    </div>-->
                                                    <div class="mb-2">
                                                        <label for="form-name">Form Name</label>
                                                        <div style="display:flex; flex-direction:row; justify-content:baseline; align-items:baseline;">
                                                            <select name="form-type" id="form-type" class="form-select form-select-sm">
                                                                <?php
                                                                $data = $admin->fetchAvailableformTypes();
                                                                foreach ($data as $ft) {
                                                                ?>
                                                                    <option value="<?= $ft['id'] ?>"><?= $ft['name'] ?></option>
                                                                <?php
                                                                }
                                                                ?>
                                                            </select>
                                                            <span class="bi bi-plus-circle-fill text-success" style="margin-inline-start: 5px;" data-bs-toggle="modal" data-bs-target="#addFormType"></span>
                                                        </div>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="form-price">Price</label>
                                                        <input type="text" class="form-control form-control-sm" name="form-price" id="form-price" placeholder="0.00">
                                                    </div>
                                                    <div>
                                                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>

                                        <!-- Add form type modal form-->
                                        <div class="modal fade" id="addFormType" tabindex="-1" aria-labelledby="addFormTypeLabel" aria-hidden="true">
                                            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h1 class="modal-title fs-5" id="staticBackdropLabel">Form Type</h1>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <form id="formTypeForm" action="#" method="post" class="">
                                                            <div class="card">
                                                                <h5 class="card-header">Add Form</h5>
                                                                <div class="card-body">
                                                                    <div class="mb-2">
                                                                        <label for="form-name">Action</label>
                                                                        <div style="display:flex; flex-direction:row; justify-content:baseline; align-items:baseline;">
                                                                            <select name="form-type" id="form-type" class="form-select form-select-sm">
                                                                                <option value="add">Add</option>
                                                                                <option value="Update">Update</option>
                                                                            </select>
                                                                        </div>
                                                                    </div>
                                                                    <div class="mb-3">
                                                                        <label for="form-price">Form Name</label>
                                                                        <input type="text" class="form-control form-control-sm" name="form-price" id="form-price" placeholder="0.00">
                                                                    </div>
                                                                    <div>
                                                                        <button type="submit" class="btn btn-primary btn-sm">Add</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </form>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="button" class="btn btn-primary">Understood</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--Admission Pane-->
                        <div class="tab-pane fade" id="admission-tab-pane" role="tabpanel" aria-labelledby="admission-tab" tabindex="0">
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </main><!-- End #main -->

    <?= require_once("inc/footer-section.php") ?>
    <script src="js/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function() {
            $("#formTypeForm").submit(function(e) {
                e.preventDefault();
                $.ajax({
                    type: "POST",
                    url: "endpoint/admin-forms-price",
                    data: new FormData(this),
                    success: function(result) {
                        console.log(result);
                        if (result.success) {

                        }
                    },
                    error: function(error) {
                        console.log(error);
                    }
                });
            });

            $(".edit-form").click(function(e) {
                alert($(this).attr("id"));
            });

            $(".delete-form").click(function(e) {
                alert($(this).attr("id"));
            });
        });
    </script>

</body>

</html>