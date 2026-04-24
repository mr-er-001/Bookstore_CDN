<?php
include 'dbb.php';

$search = $_GET['q'] ?? '';

if($search != ''){
    $stmt = $conn->prepare("SELECT id, company_name FROM client WHERE company_name LIKE ? OR id LIKE ? LIMIT 10");
    $like = "%$search%";
    $stmt->bind_param("ss", $like, $like);
    $stmt->execute();
    $result = $stmt->get_result();

    if($result->num_rows > 0){
        while($row = $result->fetch_assoc()){
            echo "<a href='#' class='list-group-item list-group-item-action client-item' data-id='{$row['id']}'>{$row['company_name']}</a>";
        }
    } else {
        echo "<div class='list-group-item text-muted'>No clients found</div>";
    }
}
?>
