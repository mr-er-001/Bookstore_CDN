<?php
include 'dbb.php';

$q = $_GET['q'] ?? '';
$q = "%".$q."%";

$stmt = $conn->prepare("SELECT id, isbn, title FROM books WHERE title LIKE ? OR isbn LIKE ? ORDER BY title ASC LIMIT 10");
$stmt->bind_param("ss", $q, $q);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    while($row = $res->fetch_assoc()){
        echo "<a href='#' class='list-group-item list-group-item-action book-suggestion' 
                 data-id='{$row['id']}' 
                 data-name='{$row['title']}'>
                 {$row['isbn']} - {$row['title']}
              </a>";
    }
}else{
    echo "<div class='list-group-item text-muted'>No books found</div>";
}
