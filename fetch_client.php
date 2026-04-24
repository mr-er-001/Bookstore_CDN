<?php
include 'dbb.php';

if (isset($_POST['search'])) {
    $search = mysqli_real_escape_string($conn, $_POST['search']);
    $query = mysqli_query($conn, "SELECT id, company_name FROM client WHERE company_name LIKE '%$search%' LIMIT 10");

    while ($row = mysqli_fetch_assoc($query)) {
        echo '<a href="#" class="list-group-item list-group-item-action client-item" data-id="'.$row['id'].'">'.$row['company_name'].'</a>';
    }
}
?>
