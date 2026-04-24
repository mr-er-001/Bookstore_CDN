<?php
include 'dbb.php';
$q = $_GET['q'] ?? '';
$q = "%".$q."%";

// Replace 'client_name' with your actual column in clients table
$stmt = $conn->prepare("SELECT id, company_name FROM client WHERE company_name LIKE ? ORDER BY company_name ASC LIMIT 10");
$stmt->bind_param("s", $q);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
        echo "<a href='#' class='list-group-item list-group-item-action suggestion-item' data-id='{$row['id']}' data-name='{$row['company_name']}'>{$row['company_name']}</a>";
    }
}else{
    echo "<div class='list-group-item text-muted'>No clients found</div>";
}
