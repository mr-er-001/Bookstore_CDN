<?php
include 'dbb.php';

$conditions = [];

if (!empty($_GET['publisher_id'])) {
    $publisher_id = mysqli_real_escape_string($conn, $_GET['publisher_id']);
    $conditions[] = "publisher_id = '$publisher_id'";
} elseif (!empty($_GET['publisher_name'])) {
    $publisher_name = mysqli_real_escape_string($conn, $_GET['publisher_name']);
    $conditions[] = "publisher_name LIKE '%$publisher_name%'";
}

$sql = "SELECT title, sale_price, purchase_price, quantity FROM books";
if ($conditions) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}
$sql .= " ORDER BY title ASC";

$result = mysqli_query($conn, $sql);

$grandTotal = 0; // to store total of all books

if ($result && mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $title = htmlspecialchars($row['title']);
        $sale_price = floatval($row['sale_price']);
        $purchase_price = floatval($row['purchase_price']);
        $quantity = intval($row['quantity']);
        $quantity_class = ($quantity < 0) ? 'text-danger' : '';

        $total_price = $sale_price * $quantity;
        $grandTotal += $total_price;

        echo "<tr>
                <td>{$title}</td>
                <td class='text-center'>" . number_format($sale_price, 2) . "</td>
                <td class='text-center'>" . number_format($purchase_price, 2) . "</td>
                <td class='text-center {$quantity_class}'>{$quantity}</td>
                <td class='text-center'>" . number_format($total_price, 2) . "</td>
              </tr>";
    }

    // Grand Total row
    echo "<tr class='table-dark'>
            <td colspan='4' class='text-end fw-bold'>Grand Total:</td>
            <td class='text-center fw-bold'>" . number_format($grandTotal, 2) . "</td>
          </tr>";
} else {
    echo "<tr><td colspan='5' class='text-center text-muted py-4'>
            <i class='fas fa-book fa-2x mb-2 d-block'></i>
            No books found for this publisher
          </td></tr>";
}
?>
