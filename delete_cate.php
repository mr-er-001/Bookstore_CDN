<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Convert to integer for safety

    $sql = "DELETE FROM category WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: cate_data.php"); // Redirect after successful delete
        exit;
    } else {
        echo "Error deleting record: " . mysqli_error($conn);
    }
} else {
    // If no ID is provided, redirect to the category list
    header("Location: categorydata.php");
    exit;
}
?>
