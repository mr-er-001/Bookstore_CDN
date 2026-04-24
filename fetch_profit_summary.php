<?php
include 'dbb.php';

function parseDMY($str) {
    $parts = explode('-', trim($str));
    if (count($parts) !== 3) return false;
    [$d, $m, $y] = $parts;
    return sprintf('%04d-%02d-%02d', $y, $m, $d);
}

$from = parseDMY($_GET['from'] ?? '');
$to   = parseDMY($_GET['to']   ?? '');
$group = ($_GET['group'] ?? 'day') === 'month' ? 'month' : 'day';

if (!$from || !$to) {
    echo "<tr><td colspan='8' class='text-center text-muted'>Invalid date range</td></tr>";
    exit;
}

$sql = "
    SELECT
        b.id            AS book_id,
        b.isbn,
        b.title,
        b.purchase_price,
        si.price        AS sale_price,
        si.quantity,
        si.discount,
        si.invoice_date
    FROM sale_invoice si
    INNER JOIN books b ON si.book_id = b.id
    WHERE si.invoice_date BETWEEN ? AND ?
    ORDER BY si.invoice_date ASC, b.title ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ss", $from, $to);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo "<tr><td colspan='8' class='text-center text-muted py-3'>No sales found for selected range</td></tr>";
    exit;
}

// ── Build grouped data structure ──────────────────────────────────────────────
// $data[period][book_id] = [isbn, title, qty, cost, revenue]
$data     = [];
$bookMeta = [];

while ($row = $res->fetch_assoc()) {
    $period = ($group === 'month')
        ? date('Y-m',   strtotime($row['invoice_date']))
        : date('Y-m-d', strtotime($row['invoice_date']));

    $bid = $row['book_id'];
    $qty = (int)$row['quantity'];
    $pp  = (float)$row['purchase_price'];
    $sp  = (float)$row['sale_price'];
    $disc = (float)$row['discount'];

    $spNet   = $sp - ($disc / 100) * $sp;   // price after discount
    $cost    = $pp    * $qty;
    $revenue = $spNet * $qty;

    if (!isset($data[$period][$bid])) {
        $data[$period][$bid] = ['qty' => 0, 'cost' => 0.0, 'revenue' => 0.0];
        $bookMeta[$bid]      = ['isbn' => $row['isbn'], 'title' => $row['title']];
    }

    $data[$period][$bid]['qty']     += $qty;
    $data[$period][$bid]['cost']    += $cost;
    $data[$period][$bid]['revenue'] += $revenue;
}

// ── Render ────────────────────────────────────────────────────────────────────
$html      = '';
$grandQty  = 0;
$grandCost = 0.0;
$grandRev  = 0.0;

foreach ($data as $period => $books) {

    $displayPeriod = ($group === 'month')
        ? date('M Y',    strtotime($period . '-01'))
        : date('d M Y',  strtotime($period));

    $pQty  = 0;
    $pCost = 0.0;
    $pRev  = 0.0;
    $bookCount = count($books);
   // ── Period header row (full width) ────────────────────────────────────────
    $html .= "
    <tr style='background-color:#e0f0f5; border-top: 2px solid #045E70;'>
        <td colspan='8' class='fw-bold ps-3' style='color:#045E70; font-size:0.95rem;'>
            <i class='fas fa-calendar-alt me-2'></i>{$displayPeriod}
        </td>
    </tr>";

    foreach ($books as $bid => $v) {
        $profit    = $v['revenue'] - $v['cost'];
        $profitPct = $v['cost'] > 0 ? ($profit / $v['cost']) * 100 : 0;
        $cls       = $profit >= 0 ? 'profit-pos' : 'profit-neg';

        $pQty  += $v['qty'];
        $pCost += $v['cost'];
        $pRev  += $v['revenue'];

        $html .= "
        <tr>
            <td class='text-center text-muted' style='font-size:0.8rem;'>—</td>
            <td>{$bookMeta[$bid]['title']}</td>
            <td>{$bookMeta[$bid]['isbn']}</td>
            <td class='text-center'>{$v['qty']}</td>
            <td class='text-end'>" . number_format($v['cost'],    2) . "</td>
            <td class='text-end'>" . number_format($v['revenue'], 2) . "</td>
            <td class='text-end {$cls}'>" . number_format($profit,    2) . "</td>
            <td class='text-end {$cls}'>" . number_format($profitPct, 2) . "%</td>
        </tr>";
    }

    // ── Period subtotal ───────────────────────────────────────────────────────
    $pProfit  = $pRev - $pCost;
    $pPct     = $pCost > 0 ? ($pProfit / $pCost) * 100 : 0;
    $sCls     = $pProfit >= 0 ? 'profit-pos' : 'profit-neg';

    $html .= "
    <tr class='subtotal-row'>
        <td colspan='3' class='text-end'>Subtotal — {$displayPeriod}</td>
        <td class='text-center'>{$pQty}</td>
        <td class='text-end'>" . number_format($pCost, 2) . "</td>
        <td class='text-end'>" . number_format($pRev,  2) . "</td>
        <td class='text-end {$sCls}'>" . number_format($pProfit, 2) . "</td>
        <td class='text-end {$sCls}'>" . number_format($pPct,    2) . "%</td>
    </tr>";

    $grandQty  += $pQty;
    $grandCost += $pCost;
    $grandRev  += $pRev;
}

// ── Grand total ───────────────────────────────────────────────────────────────
$grandProfit = $grandRev - $grandCost;
$grandPct    = $grandCost > 0 ? ($grandProfit / $grandCost) * 100 : 0;

$html .= "
<tr class='grand-row'>
    <td colspan='3' class='text-end'>GRAND TOTAL</td>
    <td class='text-center'>{$grandQty}</td>
    <td class='text-end'>" . number_format($grandCost,   2) . "</td>
    <td class='text-end'>" . number_format($grandRev,    2) . "</td>
    <td class='text-end'>" . number_format($grandProfit, 2) . "</td>
    <td class='text-end'>" . number_format($grandPct,    2) . "%</td>
</tr>";

echo $html;
?>