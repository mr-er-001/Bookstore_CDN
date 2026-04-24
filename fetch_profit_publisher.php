<?php
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    echo "<tr><td colspan='9' class='text-center text-danger py-3'>"
       . "<i class='fas fa-exclamation-triangle me-2'></i>"
       . "PHP Error [{$errno}]: " . htmlspecialchars($errstr)
       . " in " . htmlspecialchars(basename($errfile)) . " line {$errline}"
       . "</td></tr>";
    exit;
});
set_exception_handler(function($e) {
    echo "<tr><td colspan='9' class='text-center text-danger py-3'>"
       . "<i class='fas fa-exclamation-triangle me-2'></i>"
       . "Exception: " . htmlspecialchars($e->getMessage())
       . "</td></tr>";
    exit;
});

include 'dbb.php';

function parseDMY($str) {
    $parts = explode('-', trim($str));
    if (count($parts) !== 3) return false;
    [$d, $m, $y] = $parts;
    if (!checkdate((int)$m, (int)$d, (int)$y)) return false;
    return sprintf('%04d-%02d-%02d', $y, $m, $d);
}

function row_error($msg) {
    echo "<tr><td colspan='9' class='text-center text-danger py-3'>"
       . "<i class='fas fa-exclamation-triangle me-2'></i>"
       . htmlspecialchars($msg) . "</td></tr>";
    exit;
}

// ── Inputs ────────────────────────────────────────────────────────────────────
$from      = parseDMY($_GET['from']  ?? '');
$to        = parseDMY($_GET['to']    ?? '');
$group     = ($_GET['group'] ?? 'day') === 'month' ? 'month' : 'day';
$publisher = trim($_GET['publisher'] ?? '');

if (!$from || !$to) row_error("Invalid date range. Use dd-mm-yyyy format.");

// ── Build query ───────────────────────────────────────────────────────────────
// JOIN publisher table to get publisher_name from books.publisher_id
$sql = "
    SELECT
        p.publisher_name AS publisher,
        b.id             AS book_id,
        b.isbn,
        b.title,
        b.purchase_price,
        si.price         AS sale_price,
        si.quantity,
        si.discount,
        si.invoice_date
    FROM sale_invoice si
    INNER JOIN books b       ON si.book_id    = b.id
    INNER JOIN publisher p   ON b.publisher_id = p.id
    WHERE si.invoice_date BETWEEN ? AND ?
";

$params = [$from, $to];
$types  = "ss";

if ($publisher !== '') {
    $sql     .= " AND p.publisher_name LIKE ?";
    $params[] = $publisher;
    $types   .= "s";
}

$sql .= " ORDER BY p.publisher_name ASC, si.invoice_date ASC, b.title ASC";

// ── Prepare & Execute ─────────────────────────────────────────────────────────
$stmt = $conn->prepare($sql);
if (!$stmt) row_error("Query prepare failed: " . $conn->error);

$stmt->bind_param($types, ...$params);
if (!$stmt->execute()) row_error("Query execute failed: " . $stmt->error);

// ── Fetch rows (compatible without mysqlnd) ───────────────────────────────────
$meta = $stmt->result_metadata();
if (!$meta) row_error("Could not get result metadata: " . $stmt->error);

$fields  = [];
$bindArr = [];
while ($field = $meta->fetch_field()) {
    $fields[]              = $field->name;
    $bindArr[$field->name] = null;
}
$refs = [];
foreach ($fields as $f) $refs[] = &$bindArr[$f];
call_user_func_array([$stmt, 'bind_result'], $refs);

$rows = [];
while ($stmt->fetch()) {
    $row = [];
    foreach ($fields as $f) $row[$f] = $bindArr[$f];
    $rows[] = $row;
}
$stmt->close();

if (empty($rows)) {
    echo "<tr><td colspan='9' class='text-center text-muted py-3'>"
       . "<i class='fas fa-inbox fa-lg mb-2 d-block'></i>"
       . "No sales found for the selected range</td></tr>";
    exit;
}

// ── Build data structure ──────────────────────────────────────────────────────
$data     = [];
$bookMeta = [];

foreach ($rows as $row) {
    $pub    = ($row['publisher'] !== null && $row['publisher'] !== '') ? $row['publisher'] : 'Unknown Publisher';
    $period = ($group === 'month')
        ? date('Y-m',   strtotime($row['invoice_date']))
        : date('Y-m-d', strtotime($row['invoice_date']));

    $bid  = $row['book_id'];
    $qty  = (int)$row['quantity'];
    $pp   = (float)$row['purchase_price'];
    $sp   = (float)$row['sale_price'];
    $disc = (float)$row['discount'];

    $spNet   = $sp * (1 - $disc / 100);
    $cost    = $pp    * $qty;
    $revenue = $spNet * $qty;

    if (!isset($data[$pub][$period][$bid])) {
        $data[$pub][$period][$bid] = ['qty' => 0, 'cost' => 0.0, 'revenue' => 0.0];
        $bookMeta[$bid] = ['isbn' => $row['isbn'], 'title' => $row['title']];
    }

    $data[$pub][$period][$bid]['qty']     += $qty;
    $data[$pub][$period][$bid]['cost']    += $cost;
    $data[$pub][$period][$bid]['revenue'] += $revenue;
}

// ── Render ────────────────────────────────────────────────────────────────────
$html      = '';
$grandQty  = 0;
$grandCost = 0.0;
$grandRev  = 0.0;

foreach ($data as $pub => $periods) {

    $pubQty  = 0;
    $pubCost = 0.0;
    $pubRev  = 0.0;

    $html .= "<tr class='publisher-row'>"
           . "<td colspan='9' class='ps-3'>"
           . "<i class='fas fa-building me-2'></i>"
           . htmlspecialchars($pub)
           . "</td></tr>\n";

    foreach ($periods as $period => $books) {

        $displayPeriod = ($group === 'month')
            ? date('M Y',   strtotime($period . '-01'))
            : date('d M Y', strtotime($period));

        $pQty  = 0;
        $pCost = 0.0;
        $pRev  = 0.0;

        $html .= "<tr class='period-row'>"
               . "<td class='ps-4'><i class='fas fa-calendar-alt me-2'></i>"
               . htmlspecialchars($displayPeriod) . "</td>"
               . "<td colspan='8'></td></tr>\n";

        foreach ($books as $bid => $v) {
            $profit    = $v['revenue'] - $v['cost'];
            $profitPct = $v['cost'] > 0 ? ($profit / $v['cost']) * 100 : 0;
            $cls       = $profit >= 0 ? 'profit-pos' : 'profit-neg';

            $pQty  += $v['qty'];
            $pCost += $v['cost'];
            $pRev  += $v['revenue'];

            $html .= "<tr>"
                   . "<td class='text-muted ps-5' style='font-size:0.8rem;'>—</td>"
                   . "<td class='text-muted' style='font-size:0.8rem;'>—</td>"
                   . "<td>" . htmlspecialchars($bookMeta[$bid]['title']) . "</td>"
                   . "<td>" . htmlspecialchars($bookMeta[$bid]['isbn'])  . "</td>"
                   . "<td class='text-center'>" . $v['qty'] . "</td>"
                   . "<td class='text-end'>" . number_format($v['cost'],    2) . "</td>"
                   . "<td class='text-end'>" . number_format($v['revenue'], 2) . "</td>"
                   . "<td class='text-end $cls'>" . number_format($profit,    2) . "</td>"
                   . "<td class='text-end $cls'>" . number_format($profitPct, 2) . "%</td>"
                   . "</tr>\n";
        }

        // Period subtotal
        $pProfit = $pRev - $pCost;
        $pPct    = $pCost > 0 ? ($pProfit / $pCost) * 100 : 0;
        $sCls    = $pProfit >= 0 ? 'profit-pos' : 'profit-neg';

        $html .= "<tr class='subtotal-period'>"
               . "<td colspan='4' class='text-end fw-semibold'>Subtotal &mdash; " . htmlspecialchars($displayPeriod) . "</td>"
               . "<td class='text-center'>$pQty</td>"
               . "<td class='text-end'>" . number_format($pCost,   2) . "</td>"
               . "<td class='text-end'>" . number_format($pRev,    2) . "</td>"
               . "<td class='text-end $sCls'>" . number_format($pProfit, 2) . "</td>"
               . "<td class='text-end $sCls'>" . number_format($pPct,    2) . "%</td>"
               . "</tr>\n";

        $pubQty  += $pQty;
        $pubCost += $pCost;
        $pubRev  += $pRev;
    }

    // Publisher subtotal
    $pubProfit = $pubRev - $pubCost;
    $pubPct    = $pubCost > 0 ? ($pubProfit / $pubCost) * 100 : 0;

    $html .= "<tr class='subtotal-pub'>"
           . "<td colspan='4' class='text-end'>Total &mdash; " . htmlspecialchars($pub) . "</td>"
           . "<td class='text-center'>$pubQty</td>"
           . "<td class='text-end'>" . number_format($pubCost,   2) . "</td>"
           . "<td class='text-end'>" . number_format($pubRev,    2) . "</td>"
           . "<td class='text-end'>" . number_format($pubProfit, 2) . "</td>"
           . "<td class='text-end'>" . number_format($pubPct,    2) . "%</td>"
           . "</tr>\n";

    $grandQty  += $pubQty;
    $grandCost += $pubCost;
    $grandRev  += $pubRev;
}

// Grand Total
$grandProfit = $grandRev - $grandCost;
$grandPct    = $grandCost > 0 ? ($grandProfit / $grandCost) * 100 : 0;

$html .= "<tr class='grand-row'>"
       . "<td colspan='4' class='text-end'>GRAND TOTAL</td>"
       . "<td class='text-center'>$grandQty</td>"
       . "<td class='text-end'>" . number_format($grandCost,   2) . "</td>"
       . "<td class='text-end'>" . number_format($grandRev,    2) . "</td>"
       . "<td class='text-end'>" . number_format($grandProfit, 2) . "</td>"
       . "<td class='text-end'>" . number_format($grandPct,    2) . "%</td>"
       . "</tr>\n";

echo $html;
?>