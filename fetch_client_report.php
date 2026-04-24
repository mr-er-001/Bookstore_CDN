<?php
// Enable error reporting
error_reporting(E_ALL);       // Report all PHP errors
ini_set('display_errors', 1); // Display errors on the browser
ini_set('display_startup_errors', 1); // Show errors during PHP startup
?>


<?php
include 'dbb.php';

// Get input parameters
$client = isset($_GET['client']) ? intval($_GET['client']) : 0;
$from = !empty($_GET['from']) ? DateTime::createFromFormat('d-m-Y', $_GET['from'])->format('Y-m-d') : '';
$to   = !empty($_GET['to']) ? DateTime::createFromFormat('d-m-Y', $_GET['to'])->format('Y-m-d') : '';

if (!$client) {
    echo "<tr><td colspan='6' class='text-center text-danger'>Client not selected</td></tr>";
    exit;
}

if (!$from || !$to) {
    echo "<tr><td colspan='6' class='text-center text-danger'>Invalid date range</td></tr>";
    exit;
}

// SQL: group by invoice_no with discounts
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
    COUNT(*) AS item_count
FROM sale_invoice si
INNER JOIN client c ON si.client_id = c.id
WHERE si.invoice_date BETWEEN ? AND ?
  AND si.client_id = ?
GROUP BY si.invoice_no, si.client_id, c.company_name
ORDER BY invoice_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $from, $to, $client);
$stmt->execute();
$res = $stmt->get_result();

$totalNet = 0;

if ($res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $totalNet += $row['net_total'];
        // Use invoice_no as parameter
        $invoiceLink = "sale_in.php?invoice_no=" . urlencode($row['invoice_no']);

      echo "<tr>
        <td><a href='{$invoiceLink}'>" . htmlspecialchars($row['client_name']) . "</a></td>
        <td>" . htmlspecialchars($row['invoice_no']) . "</td>
        <td>" . date('d M Y', strtotime($row['invoice_date'])) . "</td>
        <td>" . number_format($row['total_price'], 2) . "</td>
        <td>" . number_format($row['net_total'], 2) . "</td>

        <td>

            <!-- View button -->
            <a href='sale_in.php?invoice_no=" . urlencode($row['invoice_no']) . "' 
               class='btn btn-primary btn-sm'>
               View
            </a>

            <!-- Print button -->
            <a href='sale_in.php?invoice_no=" . urlencode($row['invoice_no']) . "' 
               class='btn btn-danger btn-sm' target='_blank'>
               Print
            </a>

            <!-- Return button -->
            <form method='post' action='pro_return.php' 
                  style='display:inline-block;'
                  onsubmit='return confirm(\"Are you sure you want to return this invoice?\");'>
                <input type='hidden' name='invoice_no' value='" . $row['invoice_no'] . "'>
                <button type='submit' class='btn btn-warning btn-sm'>Return</button>
            </form>

        </td>
      </tr>";

    }

    // Total row
    echo "<tr class='table-secondary fw-bold'>
            <td colspan='4' class='text-end'>Total Net:</td>
            <td>" . number_format($totalNet, 2) . "</td>
            <td></td>
          </tr>";

} else {
    echo "<tr><td colspan='6' class='text-center text-muted'>No sales found</td></tr>";
}
?>
