<!--Vendors Pane-->
<div class="tab-pane fade" id="vendors-tab-pane" role="tabpanel" aria-labelledby="vendors-tab" tabindex="0">
    <div class="container mt-4">
        <div class="row">
            <div class="col-lg-6">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col" style="width: 250px;">Vendor Name</th>
                            <th scope="col">Phone Number</th>
                            <th scope="col"></th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $vendors = $admin->fetchAllVendorDetails();
                        if (!empty($vendors)) {
                            $i = 1;
                            foreach ($vendors as $vendor) {
                        ?>
                                <tr>
                                    <th scope="row"><?= $i ?></th>
                                    <td><?= $vendor["vendor_name"] ?></td>
                                    <td><?= $vendor["phone_number"] ?></td>
                                    <td id="<?= $vendor["id"] ?>" class="edit-vendor"><span style="cursor:pointer;" class="bi bi-pencil-square text-primary" title="Edit <?= $vendor["vendor_name"] ?>"></span></td>
                                    <td id="<?= $vendor["id"] ?>" class="delete-vendor"><span style="cursor:pointer;" class="bi bi-trash text-danger" title="Delete <?= $vendor["vendor_name"] ?>"></span></td>
                                </tr>
                        <?php
                                $i++;
                            }
                        }
                        ?>
                        <tr></tr>
                    </tbody>
                </table>
            </div>
            <div class="col-lg-1">
            </div>
            <div class="col-lg-5">
                <form id="addOrUpdateVendorForm" method="post" enctype="multipart/form-data">
                    <div class="card">
                        <h5 class="card-header">Add New Vendor</h5>
                        <div class="card-body">
                            <div class="mb-2">
                                <label for="v-name">Name</label>
                                <input type="text" class="form-control form-control-sm" name="v-name" id="v-name" placeholder="Name">
                            </div>
                            <div style="display: flex; flex-direction:row; justify-content: space-between">
                                <div class="mb-2 me-2">
                                    <label for="v-tin">Ghana Card</label>
                                    <input type="text" class="form-control form-control-sm" name="v-tin" id="v-tin" placeholder="TIN">
                                </div>
                                <div class="mb-2">
                                    <label for="v-email">Email</label>
                                    <input type="text" class="form-control form-control-sm" name="v-email" id="v-email" placeholder="Email">
                                </div>
                            </div>
                            <div style="display: flex; flex-direction:row; justify-content: space-between">
                                <div class="mb-2 me-2">
                                    <label for="v-phone">Phone No.</label>
                                    <input type="text" class="form-control form-control-sm" name="v-phone" id="v-phone" placeholder="02441234567">
                                </div>
                                <div class="mb-3">
                                    <label for="v-address">Address</label>
                                    <textarea type="text" rows="1" class="form-control form-control-sm" name="v-address" id="form-address" placeholder="Address"></textarea>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary btn-sm" id="v-action-btn">Add</button>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" name="v-action" id="v-action" value="add">
                    <input type="hidden" name="v-id" id="v-id" value="">
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

<script>
    $(document).ready(function() {
        function resetVendorForm() {
            $("#v-id").val("");
            $("#v-name").val("");
            $("#v-tin").val("");
            $("#v-email").val("");
            $("#v-phone").val("");
            $("#v-address").val("");
        }

        $("#addOrUpdateVendorForm").on("submit", function(e) {
            e.preventDefault();

            $.ajax({
                type: "POST",
                url: "../endpoint/vendor-form",
                data: new FormData(this),
                contentType: false,
                cache: false,
                processData: false,
                success: function(result) {
                    console.log(result);
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
            resetVendorForm();
        });

        $(".edit-vendor").click(function(e) {
            let data = {
                vendor_key: $(this).attr("id")
            }

            $.ajax({
                type: "GET",
                url: "../endpoint/vendor-form",
                data: data,
                success: function(result) {
                    console.log(result);
                    if (result.success) {
                        $("#v-action").attr("value", "update");
                        $(".card-header").text("Update Form Price");
                        $("#v-action-btn").text("Update");
                        $("#v-id").val(result.message[0].id);
                        $("#v-name").val(result.message[0].vendor_name);
                        $("#v-tin").val(result.message[0].tin);
                        $("#v-email").val(result.message[0].email_address);
                        $("#v-phone").val(result.message[0].phone_number);
                        $("#v-address").val(result.message[0].address);
                    } else {
                        alert(result.message)
                    };

                },
                error: function(error) {
                    console.log(error);
                }
            });
        });

        $(".delete-vendor").click(function(e) {
            var data = {
                vendor_key: $(this).attr("id")
            }

            $.ajax({
                type: "DELETE",
                url: "../endpoint/vendor-form",
                data: data,
                success: function(result) {
                    console.log(result);
                    if (result.success) {
                        alert(result.message);
                        window.location.reload();
                    } else {
                        alert(result.message);
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
            resetVendorForm();
        });
    });
</script>