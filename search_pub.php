<?php
include 'dbb.php';

$q = trim($_GET['q'] ?? '');
if ($q === '') exit;

$like  = '%' . $q . '%';
$stmt  = $conn->prepare("
    SELECT id, publisher_name
    FROM publisher
    WHERE publisher_name LIKE ? AND status = 1
    ORDER BY publisher_name ASC
    LIMIT 10
");

if (!$stmt) exit;

$stmt->bind_param("s", $like);
$stmt->execute();
$stmt->bind_result($id, $name);

while ($stmt->fetch()) {
    echo "<div class='list-group-item publisher-suggestion' data-name='" 
       . htmlspecialchars($name, ENT_QUOTES) . "'>"
       . htmlspecialchars($name)
       . "</div>";
}

$stmt->close();
?>