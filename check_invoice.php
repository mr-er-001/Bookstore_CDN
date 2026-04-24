<?php
include 'dbb.php';
if (isset($_POST['invoice_no'])) {
    $invoice_no = $_POST['invoice_no'];
    $check = mysqli_query($conn, "SELECT id FROM purchase_invoice WHERE invoice_no = '$invoice_no' LIMIT 1");
    echo (mysqli_num_rows($check) > 0) ? "exists" : "ok";
}
?>
