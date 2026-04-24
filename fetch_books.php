<?php
include "dbb.php";

if (isset($_POST['search'])) {
    $search = mysqli_real_escape_string($conn, trim($_POST['search']));

    // Show only books where ISBN has value
    $query = mysqli_query($conn, "
        SELECT id, title, isbn, pub_short, sale_price, quantity, discount 
        FROM books 
        WHERE 
            (isbn IS NOT NULL AND isbn != '' AND isbn != '0') 
            AND (
                title LIKE '%$search%' 
                OR pub_short LIKE '%$search%'
                OR isbn LIKE '%$search%'
            )
        LIMIT 10
    ");

    if (mysqli_num_rows($query) > 0) {
        while ($row = mysqli_fetch_assoc($query)) {
            echo "
<a href='#' 
    class='list-group-item list-group-item-action book-item' 
    data-id='{$row['id']}'
    data-price='{$row['sale_price']}'
    data-stock='{$row['quantity']}'
    data-discount='{$row['discount']}'>
    {$row['title']} — <small class='text-muted'>{$row['pub_short']}</small>
</a>";
        }
    } else {
        echo "<p class='list-group-item'>No books found</p>";
    }
}

?>
