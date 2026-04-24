<?php
include 'dbb.php';

if(isset($_POST['id'], $_POST['quantity'], $_POST['total_price'], $_POST['net_price'])){
    $id = $_POST['id'];
    $quantity = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $net_price = $_POST['net_price'];

    $sql = "UPDATE purchase_invoice 
            SET quantity='$quantity', total_price='$total_price', net_price='$net_price' 
            WHERE id='$id'";

    if($conn->query($sql) === TRUE){
        echo "success";
    } else {
        echo "error: " . $conn->error;
    }
}
?>
