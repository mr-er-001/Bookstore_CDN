<?php
include 'dbb.php';

// Add status_invoice column to sale_invoice (1 = saved, 0 = draft)
$sql1 = "ALTER TABLE sale_invoice ADD COLUMN IF NOT EXISTS status_invoice TINYINT(1) NOT NULL DEFAULT 1";
$sql2 = "ALTER TABLE purchase_invoice ADD COLUMN IF NOT EXISTS status_invoice TINYINT(1) NOT NULL DEFAULT 1";

if (mysqli_query($conn, $sql1)) {
    echo "sale_invoice: status_invoice column added.<br>";
} else {
    echo "sale_invoice: " . mysqli_error($conn) . "<br>";
}

if (mysqli_query($conn, $sql2)) {
    echo "purchase_invoice: status_invoice column added.<br>";
} else {
    echo "purchase_invoice: " . mysqli_error($conn) . "<br>";
}

echo "<br>Done! You can delete this file now.";
?>
