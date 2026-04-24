<?php
include 'dbb.php';

$bookId = $_GET['book'] ?? '';
$from = date('Y-m-d', strtotime($_GET['from'] ?? ''));
$to = date('Y-m-d', strtotime($_GET['to'] ?? ''));

if(!$bookId || !$from || !$to){
    echo "<tr><td colspan='10' class='text-center text-muted'>Invalid input</td></tr>";
    exit;
}

$sql = "
SELECT b.isbn, b.title, b.purchase_price, si.price, si.quantity, si.discount, si.invoice_no, si.invoice_date
FROM sale_invoice si
INNER JOIN books b ON si.book_id = b.id
WHERE si.invoice_date BETWEEN ? AND ? 
AND b.id = ?
ORDER BY si.invoice_date ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("ssi", $from, $to, $bookId);
$stmt->execute();
$res = $stmt->get_result();

if($res->num_rows > 0){
    $totalQty = 0;
    $totalProfit = 0;
    $totalCostPrice = 0;
    $totalSellingPrice = 0;

    while($row = $res->fetch_assoc()){
        $isbn          = $row['isbn'];
        $title         = $row['title'];
        $purchasePrice = (float)$row['purchase_price']; // cost price per book
        $sellingPrice  = (float)$row['price'];          // selling price per book (before discount)
        $qty           = (int)$row['quantity'];
        $discount      = (float)$row['discount'];       // may be % or fixed — assuming % here
        $invoiceNo     = $row['invoice_no'];
        $invoiceDate   = $row['invoice_date'];

        // --- Step 1: Selling price after discount per book ---
        $discountAmountPerBook = ($discount / 100) * $sellingPrice;
        $sellingPriceAfterDiscount = $sellingPrice - $discountAmountPerBook;

        // --- Step 2: Total selling price (after discount) ---
        $totalSelling = $sellingPriceAfterDiscount * $qty;

        // --- Step 3: Total cost price ---
        $totalCost = $purchasePrice * $qty;

        // --- Step 4: Profit ---
        $profit = $totalSelling - $totalCost;

        // Accumulate totals
        $totalQty += $qty;
        $totalProfit += $profit;
        $totalCostPrice += $totalCost;
        $totalSellingPrice += $totalSelling;

        echo "<tr>
                <td>{$isbn}</td>
                <td>{$title}</td>
                <td>{$purchasePrice}</td>
                <td>{$sellingPrice}</td>
                <td>{$qty}</td>
                <td>{$discount}%</td>
                <td>{$invoiceNo}</td>
                <td>".date('d-m-Y', strtotime($invoiceDate))."</td>
                <td>".number_format($totalSelling, 2)."</td>
                <td>".number_format($profit, 2)."</td>
              </tr>";
    }

    // --- Step 5: Profit Percentage ---
    $profitPercent = $totalCostPrice > 0 ? ($totalProfit / $totalCostPrice) * 100 : 0;

    echo "<tr class='table-secondary fw-bold'>
            <td colspan='4' class='text-end'>Total Quantity:</td>
            <td>{$totalQty}</td>
            <td colspan='2' class='text-end'>Total Selling Price:</td>
            <td>".number_format($totalSellingPrice, 2)."</td>
            <td>Total Profit:</td>
            <td>".number_format($totalProfit, 2)." (".number_format($profitPercent, 2)."%)</td>
          </tr>";
}else{
    echo "<tr><td colspan='10' class='text-center text-muted'>No sales found</td></tr>";
}
?>
