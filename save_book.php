<?php
include "dbb.php";

if (isset($_POST['save'])) {
    // Collect form data
    $invoice_no   = $_POST['isbn'];           // Invoice number
    $vendor       = $_POST['vendor'] ?? '';   // Vendor ID (NOT name, should be ID)
    $pub_date     = $_POST['pub_date'];       // Invoice date
    $book_title   = $_POST['book'] ?? '';     // Book ID (NOT name, should be ID)
    $price        = $_POST['price'];
    $quantity     = $_POST['quantity'];
    $total_price  = $_POST['total_price'];
    $discount     = $_POST['discount'];
    $net_price    = $_POST['net_price'];

    // Insert query
    $sql = "INSERT INTO purchase_invoice 
            (invoice_no, vendor_id, invoice_date, book_id, price, quantity, total_price, discount, net_price)
            VALUES ('$invoice_no', '$vendor', '$pub_date', '$book_title', '$price', '$quantity', '$total_price', '$discount', '$net_price')";

    if ($conn->query($sql) === TRUE) {
        $last_id = $conn->insert_id;
        // Redirect to view invoice
        header("Location: view_invoice.php?id=$last_id");
        exit;
    } else {
        echo "Error: " . $conn->error;
    }
}
?>
