<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']); // ✅ Safety: only integer allowed

    $sql = "DELETE FROM admin WHERE staff_id = $id";

    if (mysqli_query($conn, $sql)) {
        header("Location: user_for.php"); // ✅ redirect back to user list
        exit;
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
} else {
    header("Location: user_management.php");
    exit;
}
?>
