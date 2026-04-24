<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // Safety: Convert ID to integer

    // Delete client by ID
    $sql = "DELETE FROM client WHERE id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: client_data.php?msg=deleted");
        exit;
    } else {
        echo "Error deleting client: " . mysqli_error($conn);
    }
} else {
    header("Location: client_data.php");
    exit;
}
?>
