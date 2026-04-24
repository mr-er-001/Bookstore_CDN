<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // safety: only integer allowed

    $sql = "DELETE FROM author WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: authordata.php"); // redirect back after delete
        exit;
    } else {
        echo "Error deleting author: " . mysqli_error($conn);
    }
} else {
    header("Location: authordata.php");
    exit;
}
?>
