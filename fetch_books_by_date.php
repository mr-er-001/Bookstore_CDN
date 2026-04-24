<?php
include 'dbb.php';

$from = $_GET['from'] ?? '';
$to   = $_GET['to'] ?? '';

if (!$from || !$to) {
    echo "<tr><td colspan='6' class='text-center text-muted'>Invalid date range</td></tr>";
    exit;
}

// Convert dd-mm-yyyy → YYYY-MM-DD
$from_mysql = DateTime::createFromFormat('d-m-Y', $from)->format('Y-m-d');
$to_mysql   = DateTime::createFromFormat('d-m-Y', $to)->format('Y-m-d');

// 🔹 Group by invoice_no
$sql = "
    SELECT 
        pi.invoice_no,
        MIN(pi.id) AS invoice_id,
        MAX(pi.invoice_date) AS invoice_date,
        pi.vendor_id,
        v.company_name AS vendor_name,
        SUM(pi.quantity * pi.price) AS total_price,
        COUNT(*) AS book_count
    FROM purchase_invoice pi
    INNER JOIN vendor v ON pi.vendor_id = v.id
    WHERE pi.invoice_date BETWEEN ? AND ?
    GROUP BY pi.invoice_no, pi.vendor_id, v.company_name
    ORDER BY invoice_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from_mysql, $to_mysql);
$stmt->execute();
$result = $stmt->get_result();

$totalNet = 0;

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $totalNet += $row['total_price'];
          $invoiceLink = "sale_invoice.php?id=" . urlencode($row['invoice_no']);
//        $invoiceLink = "invoi.php?invoice_no=" . urlencode($row['invoice_no']);

        echo "<tr>
                <td>{$row['invoice_no']}</td>
                <td><a href='{$invoiceLink}'>{$row['vendor_name']}</a></td>
                <td>{$row['book_count']}</td>
                <td>" . number_format($row['total_price'], 2) . "</td>
                <td>" . date('d-m-Y', strtotime($row['invoice_date'])) . "</td>
              </tr>";
    }

    // ✅ Total row
    echo "<tr class='table-secondary fw-bold'>
            <td colspan='3' class='text-end'>Total Purchase Amount:</td>
            <td>" . number_format($totalNet, 2) . "</td>
            <td></td>
          </tr>";

    // ✅ Print button row
    echo "<tr>
            <td colspan='6' class='text-end'>
                <button class='btn btn-primary mt-3' onclick='printReport()'>
                    🖨️ Print Report
                </button>
            </td>
          </tr>";

    // ✅ Print script
    echo "<script>
            function printReport() {
                const btn = document.querySelector('button[onclick=\"printReport()\"]');
                btn.style.display = 'none';
                window.print();
                btn.style.display = 'inline-block';
            }
          </script>";

} else {
    echo "<tr><td colspan='6' class='text-center text-muted'>No invoices found</td></tr>";
}
?>
