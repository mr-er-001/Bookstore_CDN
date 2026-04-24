<?php
include 'dbb.php';

$search = $_GET['q'] ?? '';

if ($search != '') {
    $stmt = $conn->prepare("
        SELECT id, company_name 
        FROM vendor 
        WHERE company_name LIKE ? OR id LIKE ? 
        LIMIT 10
    ");
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Only show company_name, ID stays in data-id
            echo "
            <a href='#' 
               class='list-group-item list-group-item-action vendor-item' 
               data-id='{$row['id']}'>
                <strong>{$row['company_name']}</strong>
            </a>";
        }
    } else {
        echo "<div class='list-group-item text-muted'>No vendors found</div>";
    }
}
?>
