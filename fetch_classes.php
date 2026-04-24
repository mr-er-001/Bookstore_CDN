<?php
include 'dbb.php';

$query = $_GET['query'] ?? '';
if (!$query) { echo json_encode([]); exit; }

$stmt = $conn->prepare("SELECT DISTINCT class AS name FROM books WHERE class LIKE ? LIMIT 10");
$like = "%$query%";
$stmt->bind_param("s", $like);
$stmt->execute();
$result = $stmt->get_result();

$classes = [];
while ($row = $result->fetch_assoc()) {
    $classes[] = $row;
}

echo json_encode($classes);
?>
