<?php
include "dbb.php";

if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
    $sql = "SELECT id, company_name FROM vendor WHERE company_name LIKE '%$search%' LIMIT 10";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<a href="#" class="list-group-item list-group-item-action vendor-item" data-id="'.$row['id'].'">'
                 .htmlspecialchars($row['company_name']).'</a>';
        }
    } else {
        echo '<p class="list-group-item">No vendors found</p>';
    }
}
?>
