<?php
session_start();
//echo $_SERVER["HTTP_USER_AGENT"];
if (isset($_SESSION["adminLogSuccess"]) && $_SESSION["adminLogSuccess"] == true && isset($_SESSION["user"]) && !empty($_SESSION["user"])) {
} else {
    header("Location: login.php");
}

if (!isset($_GET["w"])) {
    if (isset($_SERVER['HTTP_REFERER'])) {
        // redirect the user back to the previous page
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}

use Src\Controller\AdminController;

require_once "./bootstrap.php";

$admin = new AdminController();

$result = array();
$title_var = "";
if (isset($_GET["w"]) && $_GET["w"] == 'pdfFileDownload') $result = $admin->executeDownloadQueryStmt();
?>

<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous">
</head>

<div>
    <h3 style="text-align: center;" class="m-4">Transactions Report</h3>
    <h6 style="display: flex; justify-content: space-between" class="m-4">
        <span><b>Filter By:</b> <?= $_SESSION["downloadQueryStmt"]["data"]["report-by"] == "PayMethod" ? "Payment Menthod" : $_SESSION["downloadQueryStmt"]["data"]["report-by"] ?></span>
        <span><b>Vendor:</b> <?= $_SESSION["downloadQueryStmt"]["data"]["report-by"] == "PayMethod" ? "Payment Menthod" : $_SESSION["downloadQueryStmt"]["data"]["report-by"] ?></span>
        <span><b>Date:</b> <?= $_SESSION["downloadQueryStmt"]["data"]["from-date"] . " - " . $_SESSION["downloadQueryStmt"]["data"]["to-date"]  ?></span>
    </h6>
    <table class="table table-borderless datatable table-striped table-hover" style="font-size: 12px;">
        <?php
        switch ($_GET["p"]) {
            case 'vendors-transactions':
                switch ($_GET["t"]) {
                    case "main":
        ?>
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Name</th>
                                <th scope="col">Total Sold</th>
                                <th scope="col">Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 1;
                            foreach ($result as $row) { ?>
                                <tr>
                                    <td><?= $index ?></td>
                                    <td><?= $row["title"] ?></td>
                                    <td><?= $row["total_num_sold"] ?></td>
                                    <td><?= $row["total_amount_sold"] ?></td>
                                </tr>
                            <?php
                                $index++;
                            } ?>
                        </tbody>
                    <?php
                        break;
                    case "specific":
                    ?>
                        <thead class="table-secondary">
                            <tr>
                                <th scope="col">S/N</th>
                                <th scope="col">Buyer Name</th>
                                <th scope="col">Country</th>
                                <th scope="col">Phone Number</th>
                                <th scope="col">Payment Mode</th>
                                <th scope="col">Date/Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $index = 1;
                            foreach ($result as $row) { ?>
                                <tr>
                                    <td><?= $index ?></td>
                                    <td><?= $row["first_name"] . " " . $row["last_name"] ?></td>
                                    <td><?= $row["country_name"] ?></td>
                                    <td><?= "(" . $row["country_code"] . ")" . $row["phone_number"] ?></td>
                                    <td><?= $row["payment_method"] ?></td>
                                    <td><?= $row["added_at"] ?></td>
                                </tr>
                            <?php
                                $index++;
                            } ?>
                        </tbody>
        <?php
                        break;
                }
                break;

            case '':
                # code...
                break;

            default:
                # code...
                break;
        } ?>
    </table>
</div>
<script>
    window.print();
    window.close();
</script>