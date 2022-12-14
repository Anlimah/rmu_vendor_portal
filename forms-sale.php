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
          <div class="row">

            <div class="col-lg-3">

              <div class="card" style="padding: 0 !important;">
                <div class=" card-body row" style="padding: 0px;">
                  <div class="col-md-3" style="display: flex; flex-direction: row">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people-fill"></i>
                    </div>
                  </div>
                  <div class="d-flex align-items-center col-md-9">
                    <h5 class="card-title">Sell form</h5>
                  </div>
                </div>
              </div>

              <div class="card" style="padding: 0 !important;">
                <div class=" card-body row" style="padding: 0px;">
                  <div class="col-md-3" style="display: flex; flex-direction: row">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people-fill"></i>
                    </div>
                  </div>
                  <div class="d-flex align-items-center col-md-9">
                    <h5 class="card-title">Sales Stats</h5>
                  </div>
                </div>
              </div>

            </div>

            <div class="col-lg-9">

              <div class="card mb-4">

                <div class="card-body">

                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4">Buying an Admission Form</h5>
                    <p class="text-center small">Enter buyer personal details</p>
                  </div>

                  <div style="display: flex !important; flex-direction: row !important; justify-content: center !important;">
                    <div class="col-5">

                      <!-- Personal Details -->
                      <form class="row g-3 needs-validation" novalidate>
                        <div class="col-12">
                          <label for="yourName" class="form-label">First Name</label>
                          <input type="text" name="name" class="form-control" id="yourName" required>
                          <div class="invalid-feedback">Please, enter buyer first name!</div>
                        </div>

                        <div class="col-12">
                          <label for="yourEmail" class="form-label">Last Name</label>
                          <input type="email" name="email" class="form-control" id="yourEmail" required>
                          <div class="invalid-feedback">Please enter buyer last name!</div>
                        </div>

                        <div class="col-12">
                          <div style="display:flex !important; flex-direction:row !important; justify-content: flex-start !important;">
                            <label class="form-label" for="country" style="margin-right: 10px; width: 45%">Country Code</label>
                            <label class="form-label" style="float:left" for="phone-number">Phone Number</label>
                          </div>
                          <div style="display:flex !important; flex-direction:row !important; justify-content: space-between !important">
                            <input name="country" id="country" value="<?= '(' . COUNTRIES[83]["code"] . ') ' . COUNTRIES[83]["name"]  ?>" title="Choose country and country code" class="form-control" list="address-country-list" style="margin-right: 10px; width: 60%" placeholder="Type for options" required>
                            <datalist id="address-country-list">
                              <?php
                              foreach (COUNTRIES as $cn) {
                                echo '<option value="(' . $cn["code"] . ') ' . $cn["name"] . '">(' . $cn["code"] . ') ' . $cn["name"] . '</option>';
                              }
                              ?>
                            </datalist>
                            <input name="phone_number" id="phone_number" maxlength="10" title="Provide your Provide Number" class="form-control" style="width: 70%" type="tel" placeholder="0244123123" required>
                          </div>
                        </div>
                        <div class="col-12 mt-4 mb-4">
                          <button class="btn btn-primary w-100" type="submit">Verify Details</button>
                        </div>
                      </form>

                      <!-- Buyer phone number check -->
                      <form class="row g-3 needs-validation" novalidate style="display: none;">
                        <p class="mb-4">Enter the verification code we sent to your phone.</p>
                        <div class="mb-4" style="display:flex !important; flex-direction:row !important; justify-content: space-around !important; align-items:baseline">
                          <label class="form-label" for="email_addr">RMU - </label>
                          <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num1" placeholder="0" required>
                          <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num2" placeholder="0" required>
                          <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num3" placeholder="0" required>
                          <input class="form-control num" type="text" maxlength="1" style="width:50px; text-align:center" name="code[]" id="num4" placeholder="0" required>
                        </div>
                        <div class="col-12 mb-4">
                          <button class="btn btn-primary w-100" type="submit" id="submitBtn">
                            Verify
                          </button>
                        </div>
                        <input class="form-control" type="hidden" name="_vSMSToken" value="<?= $_SESSION["_verifySMSToken"]; ?>">
                        <a href="step4.php">Change number</a>
                      </form>

                    </div>
                  </div>

                </div>

              </div>

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