<?php
include 'dbb.php'; // DB connection

$class = $_GET['class'] ?? '';
if (!$class) { echo json_encode([]); exit; }

$stmt = $conn->prepare("SELECT isbn, title FROM books WHERE class = ?");
$stmt->bind_param("s", $class);
$stmt->execute();
$result = $stmt->get_result();

$books = [];
while ($row = $result->fetch_assoc()) {
    $books[] = $row;
}
echo json_encode($books);
?>
