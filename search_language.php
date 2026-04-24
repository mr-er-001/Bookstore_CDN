<?php
include 'dbb.php';

$q = isset($_GET['q']) ? trim($_GET['q']) : '';
if (!$q) exit;

$stmt = $conn->prepare("SELECT DISTINCT category FROM books WHERE category LIKE CONCAT('%', ?, '%') LIMIT 5");
$stmt->bind_param("s", $q);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    echo '<div class="list-group-item suggestion-item" data-language="'.htmlspecialchars($row['category']).'">'
        .htmlspecialchars($row['category']).'</div>';
}

$stmt->close();
$conn->close();
?>
