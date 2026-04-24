<?php
include 'dbb.php';

$vendor_id   = $_GET['vendor_id'] ?? null;
$vendor_name = $_GET['vendor_name'] ?? null;
$start = !empty($_GET['start']) ? date('Y-m-d', strtotime(str_replace('/', '-', $_GET['start']))) : null;
$end   = !empty($_GET['end'])   ? date('Y-m-d', strtotime(str_replace('/', '-', $_GET['end'])))   : null;

$sql = "SELECT 
            vt.id,
            vt.tdate,
            vt.debit_amount,
            vt.credit_amount,
            vt.bank_name,
            vt.chk_no,
            vt.transaction_id,
            v.company_name,
            v.total_amount,
            CASE vt.transection_mode
                WHEN 1 THEN 'Cash'
                WHEN 2 THEN 'Check'
                WHEN 3 THEN 'Draft'
                WHEN 4 THEN 'Online Transfer'
                WHEN 5 THEN 'Return'
                ELSE 'Unknown'
            END AS transection_mode
        FROM vendor_transactions vt
        LEFT JOIN vendor v ON vt.vendor_id = v.id
        WHERE 1";


if ($vendor_id) {
    $sql .= " AND vt.vendor_id = " . intval($vendor_id);
} elseif ($vendor_name) {
    $sql .= " AND v.company_name LIKE '%" . mysqli_real_escape_string($conn, $vendor_name) . "%'";
}

if ($start && $end) {
    $sql .= " AND vt.tdate BETWEEN '$start' AND '$end'";
} elseif ($start) {
    $sql .= " AND vt.tdate >= '$start'";
} elseif ($end) {
    $sql .= " AND vt.tdate <= '$end'";
}

$sql .= " ORDER BY vt.tdate ASC, vt.id ASC";
$result = mysqli_query($conn, $sql);

if (!$result || mysqli_num_rows($result) == 0) {
    echo "<tr><td colspan='10' class='text-center'>No records found</td></tr>";
    exit;
}

$running_balance = 0; // Initialize running balance

while ($row = mysqli_fetch_assoc($result)) {
    $debit  = (float)$row['debit_amount'];
    $credit = (float)$row['credit_amount'];

    // Compute running balance:
    // Debit increases balance (vendor owes more), Credit decreases balance (vendor paid)
    $running_balance += $debit - $credit;

    echo "<tr>
        <td>{$row['company_name']}</td>
        <td>{$row['tdate']}</td>
        <td>" . number_format($debit, 2) . "</td>
        <td>" . number_format($credit, 2) . "</td>
        <td>" . number_format($running_balance, 2) . "</td>
        <td>{$row['transection_mode']}</td>
        <td>{$row['bank_name']}</td>
        <td>{$row['chk_no']}</td>
        <td>{$row['transaction_id']}</td>
    </tr>";
}

?>
