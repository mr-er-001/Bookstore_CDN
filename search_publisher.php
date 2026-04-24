<?php
include 'dbb.php';

if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search = mysqli_real_escape_string($conn, $_GET['q']);
    
    $query = "SELECT id, publisher_name 
              FROM publisher
              WHERE publisher_name LIKE '%$search%' 
              ORDER BY publisher_name ASC 
              LIMIT 10";
    
    $result = mysqli_query($conn, $query);
    
    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo '<div class="list-group-item list-group-item-action publisher-item" data-id="' . 
                 $row['id'] . '" style="cursor: pointer;">' . 
                 htmlspecialchars($row['publisher_name']) . '</div>';
        }
    } else {
        echo '<div class="list-group-item text-muted">No publishers found</div>';
    }
}
?>
