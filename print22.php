<?php
/**
 * ESC/POS INVOICE PRINT – SINGLE FILE
 * Printer: POS80 (80mm)
 */
include 'dbb.php';
$invoice_no = trim($_GET['invoice_no']);
$sql = "
    SELECT si.id, si.invoice_no, si.invoice_date, si.price, si.quantity, 
           si.discount, si.discount_type, b.title AS book_title, c.company_name AS client_name
    FROM sale_invoice si
    LEFT JOIN books b ON si.book_id = b.id
    LEFT JOIN client c ON si.client_id = c.id
    WHERE TRIM(si.invoice_no) = '$invoice_no'
    ORDER BY si.id ASC
";
$items = [];
$clientName = "";
$invoiceDate = "";

$result = $conn->query($sql);
$PRINTER = "POS80";   // Change if needed

$ESC = "\x1B";
$GS  = "\x1D";

/* ---------- HELPERS ---------- */

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

/* ---------- SAMPLE DATA ---------- */

$storeName  = "IJAZ BOOK CENTER";
//$clientName = "MUGHAL BOOK STORE";
//$invoiceDate = date("d M Y");
//
//$items = [
//    [1, "Aina marozi home economics No.9th", 60, 1, 35],
//    [2, "Active & passive voice made easy", 120, 2, 30],
//    [3, "Tenses made easy", 120, 10, 30],
//    [4, "Translation made easy", 120, 23, 30],
//];

$clientName = $row['client_name'];
$invoiceDate = date("d M Y", strtotime($row['invoice_date']));
while ($row = $result->fetch_assoc()) {
        // Add each row to $items array
        $items[] = [
            $row['id'],       
            $row['book_title'], 
            $row['price'],      
            $row['quantity'],   
            $row['discount']    
        ];
        // Set client name and invoice date (same for all rows)
        
    }

/* ---------- START PRINT ---------- */

$print  = $ESC . "@";          // INIT

// STORE NAME – DOUBLE HEIGHT, CENTER
$print .= $ESC . "a" . "\x01";
$print .= $ESC . "!" . "\x10";
$print .= strtoupper($storeName) . "\n";
$print .= $ESC . "!" . "\x00";

// SMALL FONT
$print .= $ESC . "M" . "\x01";

// CLIENT NAME – LEFT
$print .= $ESC . "a" . "\x00";
$print .= strtoupper($clientName) . "\n";

// SALE INVOICE + DATE – RIGHT
$print .= $ESC . "a" . "\x02";
$print .= "SALE INVOICE\n";
$print .= "DATE: " . strtoupper($invoiceDate) . "\n";

// BACK TO LEFT
$print .= $ESC . "a" . "\x00";
$print .= str_repeat("-", 64) . "\n";

// TABLE HEADER
$print .=
    col("SR",2)."  ".
    col("BOOK NAME",24)." ".
    col("PRICE",6,'R')." ".
    col("QTY",4,'R')." ".
    col("TOTAL",6,'R')." ".
    col("DISC",5,'R')." ".
    col("NET",7,'R')."\n";

$print .= str_repeat("-", 64) . "\n";

/* ---------- ITEMS ---------- */

$grand = 0;

foreach ($items as $row) {
    [$sr, $name, $price, $qty, $disc] = $row;

    $total = $price * $qty;
    $net   = $total - ($total * $disc / 100);
    $grand += $net;

    $nameLines = wrapText($name, 24);

    // FIRST LINE
    $print .=
        col($sr,2,'R')."  ".
        col($nameLines[0],24)." ".
        col(number_format($price,2),6,'R')." ".
        col($qty,4,'R')." ".
        col(number_format($total,2),6,'R')." ".
        col($disc."%",5,'R')." ".
        col(number_format($net,2),7,'R')."\n";

    // WRAPPED LINES
    for ($i = 1; $i < count($nameLines); $i++) {
        $print .= "    " . col($nameLines[$i],24) . "\n";
    }
}

$print .= str_repeat("-", 64) . "\n";

// GRAND TOTAL – DOUBLE HEIGHT
$print .= $ESC . "!" . "\x10";
$print .= col("GRAND TOTAL:", 46, 'R');
$print .= col(number_format($grand,2), 18, 'R') . "\n";
$print .= $ESC . "!" . "\x00";

// FEED & CUT
$print .= "\n\n\n\n\n\n";
$print .= $GS . "V" . "\x00";

/* ---------- SEND TO PRINTER ---------- */

$process = popen("lp -d $PRINTER", "w");
fwrite($process, $print);
pclose($process);

echo "PRINT SENT\n";
