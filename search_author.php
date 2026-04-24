<?php
include 'dbb.php';

if (isset($_GET['q'])) {
    $q = mysqli_real_escape_string($conn, $_GET['q']);

    $sql = "SELECT id, author_name FROM author WHERE author_name LIKE '%$q%' LIMIT 10";
    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='author-item p-2 border-bottom' style='cursor:pointer;' data-id='{$row['id']}'>
                    {$row['author_name']}
                  </div>";
        }
    } else {
        echo "<div class='p-2 text-muted'>No authors found</div>";
    }
}
?>
