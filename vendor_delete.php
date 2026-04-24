<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Safety: prevent SQL injection

    $sql = "DELETE FROM vendor WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        // Redirect back to your actual vendor list page
        header("Location: vendor_data.php");
        exit;
    } else {
        echo "Error deleting vendor: " . mysqli_error($conn);
    }
} else {
    // If ID not found, also go back
    header("Location: vendor_data.php");
    exit;
}
?>
