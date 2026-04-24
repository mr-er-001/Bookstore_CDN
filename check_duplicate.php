<?php
include "dbb.php";

$isbn = trim($_POST['isbn']);
$vendor = intval($_POST['vendor']);
$title = trim($_POST['title']); // Get title

if ($isbn && $vendor && $title) {
    $stmt = $conn->prepare("SELECT id FROM books WHERE isbn = ? AND vendor_id = ? AND title = ?");
    $stmt->bind_param("sis", $isbn, $vendor, $title); // s = string, i = integer, s = string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "duplicate";
    } else {
        echo "ok";
    }
} else {
    echo "missing";
}
?>
