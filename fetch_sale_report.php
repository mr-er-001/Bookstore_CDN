<?php
include 'dbb.php';

$from = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['from'] ?? '')));
$to   = date('Y-m-d', strtotime(str_replace('/', '-', $_GET['to'] ?? '')));

if (!$from || !$to) {
    echo "<tr><td colspan='5' class='text-center text-danger'>Invalid date range</td></tr>";
    exit;
}

// Group by invoice_no only
$sql = "
    SELECT 
        si.invoice_no,
        MIN(si.id) AS invoice_id,
        MAX(si.invoice_date) AS invoice_date,
        si.client_id,
        c.company_name AS client_name,
        SUM(si.price * si.quantity) AS total_price,
        SUM(
            CASE 
                WHEN si.discount_type = 1 THEN (si.price * si.quantity) - ((si.price * si.quantity) * si.discount / 100)
                ELSE (si.price * si.quantity) - si.discount
            END
        ) AS net_total,
        COUNT(*) AS book_count
    FROM sale_invoice si
    INNER JOIN client c ON si.client_id = c.id
    WHERE si.invoice_date BETWEEN ? AND ?
      AND si.quantity > 0  -- only show invoices with remaining quantity
    GROUP BY si.invoice_no, si.client_id, c.company_name
    ORDER BY invoice_date ASC
";


$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$invoices = $stmt->get_result();

$totalNet = 0;

if ($invoices->num_rows > 0) {
    while ($inv = $invoices->fetch_assoc()) {
        $totalNet += $inv['net_total'];
        // Corrected GET parameter
       $invoiceLink = "sale_invoice.php?id=" . urlencode($inv['invoice_no']);
$returnLink = "process_return.php?invoice_no=" . urlencode($inv['invoice_no']);

echo "<tr>
        <td>{$inv['invoice_no']}</td>
        <td><a href='{$invoiceLink}'>{$inv['client_name']}</a></td>
        <td>".date('d M Y', strtotime($inv['invoice_date']))."</td>
        <td>".number_format($inv['total_price'], 2)."</td>
        <td>".number_format($inv['net_total'], 2)."</td>
        <td>
            <form method='post' action='process_return.php' onsubmit='return confirm(\"Are you sure you want to return this invoice?\");'>
                <input type='hidden' name='invoice_no' value='{$inv['invoice_no']}'>
                <button type='submit' class='btn btn-danger btn-sm'>Return</button>
            </form>
        </td>
      </tr>";
    }
} else {
    echo "<tr><td colspan='5' class='text-center text-muted'>No invoices found</td></tr>";
}
?>
