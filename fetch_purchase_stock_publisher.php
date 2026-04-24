<?php
include 'dbb.php';

function parseDMY($str) {
    $parts = explode('-', trim($str));
    if (count($parts) !== 3) return false;
    [$d, $m, $y] = $parts;
    if (!checkdate((int)$m, (int)$d, (int)$y)) return false;
    return sprintf('%04d-%02d-%02d', $y, $m, $d);
}

function row_error($msg) {
    echo "<tr><td colspan='4' class='text-center text-danger py-3'>"
       . "<i class='fas fa-exclamation-triangle me-2'></i>"
       . htmlspecialchars($msg)
       . "</td></tr>";
    exit;
}

$from      = parseDMY($_GET['from']  ?? '');
$to        = parseDMY($_GET['to']    ?? '');
$group     = ($_GET['group'] ?? 'day') === 'month' ? 'month' : 'day';
$publisher = trim($_GET['publisher'] ?? '');

if (!$from || !$to) row_error('Invalid date range. Use dd-mm-yyyy format.');

// Current stock left and stock purchase value for selected publisher (or all publishers if none selected)
$stockSql = "SELECT
                COALESCE(SUM(b.quantity), 0) AS stock_left,
                COALESCE(SUM(b.quantity * b.purchase_price), 0) AS stock_value
             FROM books b
             LEFT JOIN publisher p ON b.publisher_id = p.id";
$stockParams = [];
$stockTypes  = '';
if ($publisher !== '') {
    $stockSql .= " WHERE p.publisher_name LIKE ?";
    $stockParams[] = "%{$publisher}%";
    $stockTypes = 's';
}
$stockStmt = $conn->prepare($stockSql);
if (!$stockStmt) row_error('Stock query prepare failed: ' . $conn->error);
if ($stockTypes !== '') {
    $stockStmt->bind_param($stockTypes, ...$stockParams);
}
$stockStmt->execute();
$stockStmt->bind_result($stockLeft, $stockValue);
$stockStmt->fetch();
$stockStmt->close();

$sql = "SELECT
            p.publisher_name AS publisher,
            b.id            AS book_id,
            b.isbn,
            b.title,
            SUM(pi.quantity)  AS qty,
            SUM(pi.net_price) AS purchase_total,
            pi.invoice_date
        FROM purchase_invoice pi
        INNER JOIN books b ON pi.book_id = b.id
        INNER JOIN publisher p ON b.publisher_id = p.id
        WHERE pi.invoice_date BETWEEN ? AND ?";

$params = [$from, $to];
$types  = 'ss';

if ($publisher !== '') {
    $sql .= " AND p.publisher_name LIKE ?";
    $params[] = "%{$publisher}%";
    $types .= 's';
}

$sql .= " GROUP BY p.publisher_name, " . ($group === 'month' ? "DATE_FORMAT(pi.invoice_date, '%Y-%m')" : "DATE_FORMAT(pi.invoice_date, '%Y-%m-%d')") . " ORDER BY p.publisher_name ASC, period ASC";

$query = "SELECT * FROM (" . $sql . ") tmp"; // will not actually use, we prepare original below

// Use a stable query with period alias
$sql = "SELECT
            p.publisher_name AS publisher,
            " . ($group === 'month'
                ? "DATE_FORMAT(pi.invoice_date, '%Y-%m')"
                : "DATE_FORMAT(pi.invoice_date, '%Y-%m-%d')") . " AS period,
            SUM(pi.quantity) AS qty,
            SUM(pi.net_price) AS purchase_total
        FROM purchase_invoice pi
        INNER JOIN books b ON pi.book_id = b.id
        INNER JOIN publisher p ON b.publisher_id = p.id
        WHERE pi.invoice_date BETWEEN ? AND ?";

if ($publisher !== '') {
    $sql .= " AND p.publisher_name LIKE ?";
}
$sql .= " GROUP BY p.publisher_name, period ORDER BY p.publisher_name ASC, period ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) row_error('Query prepare failed: ' . $conn->error);
if ($publisher !== '') {
    $stmt->bind_param($types, ...$params);
} else {
    $stmt->bind_param('ss', $from, $to);
}

if (!$stmt->execute()) row_error('Query execute failed: ' . $stmt->error);

$result = $stmt->get_result();
if (!$result) row_error('Could not get result set: ' . $stmt->error);

$rows = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

if (empty($rows)) {
    echo "<tr><td colspan='4' class='text-center text-muted py-3'><i class='fas fa-inbox fa-lg mb-2 d-block'></i>No purchases found for the selected range.</td></tr>";
    exit;
}

$html = "";
$html .= "<tr class=\"stock-summary\" data-stock-left=\"" . intval($stockLeft) . "\" data-stock-value=\"" . number_format($stockValue, 2, '.', '') . "\" data-purchase-total=\"0\">";
$html .= "<td colspan='4' class=\"text-start\">Current stock left: <strong>" . intval($stockLeft) . "</strong> books</td>";
$html .= "</tr>\n";

$grandQty = 0;
$grandTotal = 0.0;

foreach ($rows as $row) {
    $grandQty += (int)$row['qty'];
    $grandTotal += (float)$row['purchase_total'];
    $html .= "<tr>";
    $html .= "<td>" . htmlspecialchars($row['publisher']) . "</td>";
    $html .= "<td>" . htmlspecialchars($row['period']) . "</td>";
    $html .= "<td class='text-center'>" . intval($row['qty']) . "</td>";
    $html .= "<td class='text-end'>" . number_format($row['purchase_total'], 2) . "</td>";
    $html .= "</tr>\n";
}

$html .= "<tr class='summary-row'>";
$html .= "<td colspan='2' class='text-end'>Grand Total</td>";
$html .= "<td class='text-center'>" . intval($grandQty) . "</td>";
$html .= "<td class='text-end'>" . number_format($grandTotal, 2) . "</td>";
$html .= "</tr>\n";

// embed summary values for client-side display
$html = str_replace('data-purchase-total="0"', 'data-purchase-total="' . number_format($grandTotal, 2, '.', '') . '"', $html);

echo $html;
?>
