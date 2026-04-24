<?php
include "dbb.php";

if (isset($_POST['query'])) {
    $query = trim($_POST['query']);
    
    if ($query === '') {
        exit; // No need to search empty
    }

    $stmt = $conn->prepare("SELECT title FROM fund_titles WHERE title LIKE CONCAT('%', ?, '%') LIMIT 5");
    $stmt->bind_param("s", $query);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        while ($row = $res->fetch_assoc()) {
            echo "<a href='#' class='list-group-item list-group-item-action suggestion-item'>" . htmlspecialchars($row['title']) . "</a>";
        }
    }
     // else {
    //     echo "<a href='#' class='list-group-item list-group-item-action disabled text-muted'>No match found</a>";
    // }
}
?>
