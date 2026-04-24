<?php
include 'dbb.php';
$invoice_no = trim($_GET['invoice_no']);
$sql = "SELECT si.id,si.invoice_no,si.invoice_date,si.price,si.quantity, si.discount, b.title AS book_title, c.company_name AS client_name FROM sale_invoice si LEFT JOIN books b ON si.book_id = b.id LEFT JOIN client c ON si.client_id = c.id WHERE TRIM(si.invoice_no) = '$invoice_no' ORDER BY si.id ASC ";
$result = $conn->query($sql);
// ---------------- CONFIG ----------------
$PRINTER_IP   = "192.168.0.252";
$PRINTER_PORT = 9100;
$ESC = "\x1B";
$GS  = "\x1D";
// ---------------- HELPERS ----------------
function col($text, $width, $align = 'L') {
    $text = strtoupper((string)$text);
    if (strlen($text) > $width) {
        $text = substr($text, 0, $width);
    }
    $pad = $width - strlen($text);
    if ($align === 'R') return str_repeat(' ', $pad) . $text;
    if ($align === 'C') {
        $l = intdiv($pad, 2);
        return str_repeat(' ', $l) . $text . str_repeat(' ', $pad - $l);
    }
    return $text . str_repeat(' ', $pad);
}

function wrapText($text, $width) {
    return str_split(strtoupper($text), $width);
}
// ---------------- DATA ----------------
$storeName   = "IJAZ BOOK CENTER";
$clientName  = "";
$invoiceDate = "";
$invoiceNo   = "";
$items       = [];
while ($row = $result->fetch_assoc()) {

    // header data sirf pehli row se
    if ($clientName === "") {
        $clientName  = $row['client_name'];
        $invoiceDate = date("d M Y", strtotime($row['invoice_date']));
        $invoiceNo   = $row['invoice_no'];
    }

    $items[] = [
        $row['book_title'],
        $row['price'],
        $row['quantity'],
        $row['discount']
    ];
}
// ---------------- BUILD RECEIPT ----------------
$print  = $ESC . "@";   // INIT
// STORE NAME – DOUBLE HEIGHT, CENTER
$print .= $ESC . "a\x01";
$print .= $ESC . "!\x10";
$print .= strtoupper($storeName) . "\n";
$print .= $ESC . "!\x00";
// SMALL FONT
$print .= $ESC . "M\x01";
// Client name
$print .= $ESC . "a\x00";
$print .= strtoupper($clientName) . "\n";
// Invoice info (right)
$print .= $ESC . "a\x02";
$print .= "SALE INVOICE\n";
$print .= "INV#: " . strtoupper($invoiceNo) . "\n";
$print .= "DATE: " . strtoupper($invoiceDate) . "\n";
// Back to left
$print .= $ESC . "a\x00";
$print .= str_repeat("-", 64) . "\n";
// Table header
$print .=
    col("SR",2)."  ".
    col("BOOK NAME",24)." ".
    col("PRICE",6,'R')." ".
    col("QTY",4,'R')." ".
    col("TOTAL",6,'R')." ".
    col("DISC",5,'R')." ".
    col("NET",7,'R')."\n";

$print .= str_repeat("-", 64) . "\n";
// ---------------- ITEMS ----------------
$grand = 0;
$sr = 1;
foreach ($items as $row) {

    [$name, $price, $qty, $disc] = $row;

    $total = $price * $qty;
    $net   = $total - ($total * $disc / 100);
    $grand += $net;

    $nameLines = wrapText($name, 24);

    // first line
    $print .=
        col($sr,2,'R')."  ".
        col($nameLines[0],24)." ".
        col(number_format($price,2),6,'R')." ".
        col($qty,4,'R')." ".
        col(number_format($total,2),6,'R')." ".
        col($disc."%",5,'R')." ".
        col(number_format($net,2),7,'R')."\n";

    // wrapped book name
    for ($i = 1; $i < count($nameLines); $i++) {
        $print .= "    " . col($nameLines[$i],24) . "\n";
    }

    $sr++;
}
// ---------------- GRAND TOTAL ----------------
$print .= str_repeat("-", 64) . "\n";
// BOLD + DOUBLE WIDTH (NO HEIGHT)
$print .= $ESC . "E\x01";   // Bold ON
$print .= $ESC . "!\x20";   // Double WIDTH only
$print .= $ESC . "a\x00";   // Left align
$line = "GRAND TOTAL :  " . number_format($grand, 2);
// Simply left align, no extra spaces
$print .= $line . "\n";
// NORMAL
$print .= $ESC . "!\x00";   // Normal size
$print .= $ESC . "E\x00";   // Bold OFF
// FEED & CUT
$print .= "\n\n\n\n\n\n";
$print .= $GS . "V\x00";
// ---------------- SEND TO PRINTER ----------------
$fp = @fsockopen($PRINTER_IP, $PRINTER_PORT, $errno, $errstr, 2);
if (!$fp) {
    die("PRINT FAILED: $errstr ($errno)");
}
fwrite($fp, $print, strlen($print));
fclose($fp);
echo "PRINT SENT";
