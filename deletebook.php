<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // safety: convert to integer

    $sql = "DELETE FROM books WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: tables.php"); // redirect back after delete
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    header("Location: tables.php");
    exit;
}
?>
