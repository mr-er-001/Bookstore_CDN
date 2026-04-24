<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // safety: convert to integer

    $sql = "DELETE FROM publisher WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: pub_data.php"); // redirect back after delete
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: pub_data.php");
    exit;
}
?>
