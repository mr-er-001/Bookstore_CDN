<?php
include 'dbb.php';

$book = $_GET['book'] ?? '';
$from = date('Y-m-d', strtotime($_GET['from'] ?? ''));
$to = date('Y-m-d', strtotime($_GET['to'] ?? ''));

if(!$book || !$from || !$to){
    echo "<tr><td colspan='5' class='text-center text-muted'>Invalid input</td></tr>";
    exit;
}

$sql = "
SELECT si.invoice_no, c.company_name AS title, si.quantity, si.invoice_date, si.price
FROM sale_invoice si
INNER JOIN books b ON si.book_id = b.id
INNER JOIN client c ON si.client_id = c.id
WHERE si.book_id = ? AND si.invoice_date BETWEEN ? AND ?
ORDER BY si.invoice_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("iss", $book, $from, $to);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    $totalQty = 0;
    $totalAmount = 0;

    while($row = $res->fetch_assoc()){
        $totalQty += $row['quantity'];
        $totalAmount += $row['price'];

        echo "<tr>
                <td>{$row['invoice_no']}</td>
                <td>{$row['title']}</td>
                <td>{$row['quantity']}</td>
                <td>".date('d M Y', strtotime($row['invoice_date']))."</td>
                <td>{$row['price']}</td>
              </tr>";
    }

    // Total row
    echo "<tr class='table-secondary fw-bold'>
            <td colspan='2' class='text-end'>Total Quantity:</td>
            <td>{$totalQty}</td>
            <td class='text-end'>Total Amount:</td>
            <td>{$totalAmount}</td>
          </tr>";

    // ✅ Print Button row
    echo "<tr>
            <td colspan='5' class='text-end'>
                <button class='btn btn-primary mt-3' onclick='window.print()'>
                    🖨️ Print Report
                </button>
            </td>
          </tr>";

}else{
    echo "<tr><td colspan='5' class='text-center text-muted'>No sales found</td></tr>";
}
?>
