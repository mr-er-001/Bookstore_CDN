<?php
include "dbb.php";

if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT id, title FROM books WHERE title LIKE '%$search%' LIMIT 10";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="#" class="list-group-item list-group-item-action book-item" data-id="'.$row['id'].'">'
                 .htmlspecialchars($row['title']).'</a>';
        }
    } else {
        echo '<p class="list-group-item">No books found</p>';
    }
}
?>
