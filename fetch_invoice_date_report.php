<?php
include 'dbb.php';

$from = date('Y-m-d', strtotime($_GET['from'] ?? ''));
$to   = date('Y-m-d', strtotime($_GET['to'] ?? ''));

if(!$from || !$to){
    echo "<tr><td colspan='6' class='text-center text-muted'>Invalid input</td></tr>";
    exit;
}

$sql = "
SELECT 
    si.invoice_no, 
    c.company_name AS client, 
    b.title AS company_name,
    si.quantity, 
    si.invoice_date, 
    si.price
FROM sale_invoice si
INNER JOIN client c ON si.client_id = c.id
INNER JOIN books b ON si.book_id = b.id
WHERE si.invoice_date BETWEEN ? AND ?
ORDER BY si.invoice_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    $totalQty = 0;
    $totalAmount = 0;

    while($row = $res->fetch_assoc()){
        $totalQty += $row['quantity'];
        $totalAmount += $row['price'];
        echo "<tr>
                <td>".htmlspecialchars($row['client'])."</td>
                <td>".htmlspecialchars($row['company_name'])."</td>
                <td>".(int)$row['quantity']."</td>
                <td>".htmlspecialchars($row['invoice_no'])."</td>
                <td>".date('d M Y', strtotime($row['invoice_date']))."</td>
                <td>".number_format($row['price'], 2)."</td>
              </tr>";
    }

    // Totals row
    echo "<tr class='table-secondary fw-bold'>
            <td class='text-end' colspan='2'>Total:</td>
            <td>{$totalQty}</td>
            <td colspan='2'></td>
            <td>".number_format($totalAmount, 2)."</td>
          </tr>";

    // ✅ Print Button Row
    echo "<tr>
            <td colspan='6' class='text-end'>
                <button class='btn btn-primary mt-3' onclick='printReport()'>
                    🖨️ Print Report
                </button>
            </td>
          </tr>";

    // ✅ Print Script
    echo "<script>
            function printReport() {
                const btn = document.querySelector('button[onclick=\"printReport()\"]');
                btn.style.display = 'none';
                window.print();
                btn.style.display = 'inline-block';
            }
          </script>";

}else{
    echo "<tr><td colspan='6' class='text-center text-muted'>No invoices found</td></tr>";
}
?>
