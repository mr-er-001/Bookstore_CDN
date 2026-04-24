<?php
/**
 * FINAL ESC/POS NETWORK PRINT
 * POS80 / POS58 – 80MM
 */

$PRINTER_IP   = "192.168.0.252";   // <-- CHANGE THIS
$PRINTER_PORT = 9100;

$ESC = "\x1B";
$GS  = "\x1D";

/* ---------------- HELPERS ---------------- */

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

/* ---------------- DATA ---------------- */

$items = [
    [1, "Aina Marozi Home Economics Class 9", 60, 2],
    [2, "Active And Passive Voice Made Easy", 120, 1],
];

$date = date("d-m-Y");

/* ---------------- BUILD RECEIPT ---------------- */

$out  = $ESC . "@";                     // INIT
$out .= $ESC . "a\x01";                 // CENTER
$out .= $ESC . "!\x10";                 // DOUBLE HEIGHT
$out .= "IJAZ BOOK CENTER\n";
$out .= $ESC . "!\x00";                 // NORMAL

$out .= $ESC . "a\x00";                 // LEFT
$out .= "MUGHAL BOOK STORE\n";

$out .= $ESC . "a\x02";                 // RIGHT
$out .= "SALE INVOICE\n";
$out .= "DATE: $date\n";

$out .= $ESC . "a\x00";
$out .= str_repeat("-", 48) . "\n";

/* TABLE HEADER */
$out .=
    col("SR",2) .
    col("BOOK NAME",22) .
    col("PRICE",7,'R') .
    col("QTY",4,'R') .
    col("NET",7,'R') . "\n";

$out .= str_repeat("-", 48) . "\n";

/* ITEMS */
$grand = 0;

foreach ($items as $i) {
    [$sr, $name, $price, $qty] = $i;
    $net = $price * $qty;
    $grand += $net;

    $lines = str_split(strtoupper($name), 22);

    foreach ($lines as $k => $line) {
        if ($k === 0) {
            $out .=
                col($sr,2) .
                col($line,22) .
                col(number_format($price,2),7,'R') .
                col($qty,4,'R') .
                col(number_format($net,2),7,'R') . "\n";
        } else {
            $out .= col("",2) . col($line,22) . "\n";
        }
    }
}

$out .= str_repeat("-", 48) . "\n";
$out .= col("GRAND TOTAL",30) . col(number_format($grand,2),18,'R') . "\n";

/* FEED + CUT */
$out .= "\n\n\n\n\n\n";
$out .= $GS . "V\x00";

/* ---------------- SEND TO PRINTER ---------------- */

$fp = @fsockopen($PRINTER_IP, $PRINTER_PORT, $errno, $errstr, 2);

if (!$fp) {
    http_response_code(500);
    echo "PRINT FAILED: $errstr ($errno)";
    exit;
}

fwrite($fp, $out);
fclose($fp);

echo "PRINT SENT";
