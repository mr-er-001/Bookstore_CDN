<?php
// --- Enable error reporting ---
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

// --- Include database ---
include 'dbb.php';

// --- Get input parameters ---
$client  = isset($_GET['client'])  ? intval($_GET['client']) : 0;
$invoice = $_GET['invoice'] ?? '';
$from    = !empty($_GET['from']) ? DateTime::createFromFormat('d-m-Y', $_GET['from'])->format('Y-m-d') : '';
$to      = !empty($_GET['to'])   ? DateTime::createFromFormat('d-m-Y', $_GET['to'])->format('Y-m-d') : '';

// --- Validation ---
if (!$client && !$invoice && (!$from || !$to)) {
    echo "<tr><td colspan='5' class='text-center text-muted'>Please enter client, invoice, or date range</td></tr>";
    exit;
}

// --- Base SQL ---
$sql = "
SELECT 
    si.invoice_no,
    si.client_id,
    c.company_name AS client_name,
    MAX(si.invoice_date) AS invoice_date,
    SUM(
        CASE
            WHEN si.discount_type = 1 THEN (si.price * si.quantity) * (1 - si.discount / 100)
            ELSE (si.price * si.quantity) - si.discount
        END
    ) AS net_total
FROM sale_invoice si
INNER JOIN client c ON si.client_id = c.id
WHERE 1
";

$params = [];
$types  = '';

// --- Add conditions dynamically ---
if ($client) {
    $sql .= " AND si.client_id = ?";
    $params[] = $client;
    $types .= 'i';
}

if ($invoice) {
    $sql .= " AND si.invoice_no LIKE ?";
    $params[] = "%" . $invoice . "%";
    $types .= 's';
}

if ($from && $to) {
    $sql .= " AND si.invoice_date BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
    $types .= 'ss';
}

// --- Group and order ---
$sql .= " GROUP BY si.invoice_no, si.client_id, c.company_name
          ORDER BY invoice_date ASC";

// --- Prepare and execute ---
$stmt = $conn->prepare($sql);

if ($params) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$res = $stmt->get_result();

// --- Display results ---
$totalNet = 0;

if ($res && $res->num_rows > 0) {
    while ($row = $res->fetch_assoc()) {
        $totalNet += $row['net_total'];
        $invoiceLink = "sale_in.php?invoice_no=" . urlencode($row['invoice_no']);

        echo "<tr>
                <td><a href='{$invoiceLink}'>" . htmlspecialchars($row['client_name']) . "</a></td>
                <td>" . htmlspecialchars($row['invoice_no']) . "</td>
                <td>" . date('d-m-Y', strtotime($row['invoice_date'])) . "</td>
                <td>" . number_format($row['net_total'], 2) . "</td>
                <td>
                    <form method='post' action='proc_return.php' onsubmit='return confirm(\"Are you sure you want to return this invoice?\");'>
                        <input type='hidden' name='invoice_no' value='" . $row['invoice_no'] . "'>
                        <button type='submit' class='btn btn-warning btn-sm'>Return</button>
                    </form>
                </td>
              </tr>";
    }

    echo "<tr class='table-secondary fw-bold'>
            <td colspan='3' class='text-end'>Total Net Sale:</td>
            <td>" . number_format($totalNet, 2) . "</td>
            <td></td>
          </tr>";

} else {
    echo "<tr><td colspan='5' class='text-center text-muted'>No sales found</td></tr>";
}
?>
