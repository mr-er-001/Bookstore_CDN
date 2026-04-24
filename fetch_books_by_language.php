<?php
include 'dbb.php';

if (!isset($_GET['category']) || empty($_GET['category'])) {
    echo "<tr><td colspan='2' class='text-center text-danger'>Invalid category</td></tr>";
    exit;
}

$category = mysqli_real_escape_string($conn, $_GET['category']);

$sql = "SELECT isbn, title FROM books WHERE category = '$category'";
$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        echo "<tr>
                <td>{$row['isbn']}</td>
                <td>{$row['title']}</td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='2' class='text-center text-muted'>No books found for '$category'</td></tr>";
}
?>
