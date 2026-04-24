<?php
include 'dbb.php';

// GET parameters
$client_id   = $_GET['client_id'] ?? null;
$client_name = $_GET['client_name'] ?? null;
$start = !empty($_GET['start']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_GET['start']))) : null;
$end   = !empty($_GET['end'])   ? date('Y-m-d', strtotime(str_replace('/', '-', $_GET['end'])))   : null;

// Build WHERE clause manually like vendor code
$sql = "SELECT 
            t.id,
            t.client_id, 
            c.company_name, 
            t.tdate, 
            t.debit_amount, 
            t.credit_amount, 
            CASE t.transection_mode
                WHEN 1 THEN 'Cash'
                WHEN 2 THEN 'Check'
                WHEN 3 THEN 'Draft'
                WHEN 4 THEN 'Online Transfer'
                WHEN 5 THEN 'Return'
                ELSE 'Unknown'
            END AS transection_mode,
            t.bank_name, 
            t.chk_no, 
            t.transaction_id
        FROM client_transactions t
        LEFT JOIN client c ON t.client_id = c.id
        WHERE 1";  // ✅ Always true to start


// Add filters
if ($client_id) {
    $sql .= " AND t.client_id = " . intval($client_id);
} elseif ($client_name) {
    $sql .= " AND c.company_name LIKE '%" . mysqli_real_escape_string($conn, $client_name) . "%'";
}

if ($start && $end) {
    $sql .= " AND t.tdate BETWEEN '$start' AND '$end'";
} elseif ($start) {
    $sql .= " AND t.tdate >= '$start'";
} elseif ($end) {
    $sql .= " AND t.tdate <= '$end'";
}

$sql .= " ORDER BY t.tdate ASC, t.id ASC";

// ✅ Run query
$result = mysqli_query($conn, $sql);

// ✅ Handle no results
if (!$result || mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='9' class='text-center'>No records found</td></tr>";
    exit;
}

// ✅ Running balance logic
$running_balance = 0;

while ($row = mysqli_fetch_assoc($result)) {
    $debit  = (float)$row['debit_amount'];
    $credit = (float)$row['credit_amount'];

    // Debit increases, Credit decreases
    $running_balance += $debit - $credit;

    echo "<tr>
            <td>{$row['company_name']}</td>
            <td>{$row['tdate']}</td>
            <td>" . number_format($debit, 2) . "</td>
            <td>" . number_format($credit, 2) . "</td>
            <td>" . number_format($running_balance, 2) . "</td>
            <td>{$row['transection_mode']}</td>
          </tr>";
}
?>
