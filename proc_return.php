<?php
include 'dbb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['invoice_no'])) {

    $invoice_no = $_POST['invoice_no'];

    // Fetch invoice items
    $itemsRes = mysqli_query($conn, "SELECT * FROM sale_invoice WHERE invoice_no='{$invoice_no}'");
    if (mysqli_num_rows($itemsRes) === 0) {
        die("❌ Invoice not found");
    }

    // Get client_id from first row
    $row = mysqli_fetch_assoc($itemsRes);
    $client_id = $row['client_id'];

    // Reset pointer
    $itemsRes = mysqli_query($conn, "SELECT * FROM sale_invoice WHERE invoice_no='{$invoice_no}'");

    // Get next return number
    $last_return_row = mysqli_fetch_row(mysqli_query($conn, "SELECT MAX(return_no) FROM sale_return_invoice"));
    $next_return = ($last_return_row[0]) ? ($last_return_row[0] + 1) : 1;

    $grand_total = 0;

    while ($item = mysqli_fetch_assoc($itemsRes)) {
        $book_id = $item['book_id'];
        $qty     = $item['quantity'];
        $price   = $item['price'];
        $discount = $item['discount'];
        $discount_type = (int)$item['discount_type'];

        $total_price = $price * $qty;

        if ($discount_type === 1) {
            $net_price = $total_price - ($total_price * $discount / 100);
        } else {
            $net_price = $total_price - $discount;
        }
        if ($net_price < 0) $net_price = 0;

        $grand_total += $net_price;

        // Update stock
        mysqli_query($conn, "UPDATE books SET quantity = quantity + {$qty} WHERE id='{$book_id}'");

        // Insert into sale_return_invoice
        mysqli_query($conn, "
            INSERT INTO sale_return_invoice 
                (return_no, invoice_no, client_id, book_id, quantity, price, discount, discount_type, total_price, net_price, return_date)
            VALUES 
                ('{$next_return}', '{$invoice_no}', '{$client_id}', '{$book_id}', '{$qty}', '{$price}', '{$discount}', '{$discount_type}', '{$total_price}', '{$net_price}', NOW())
        ");

        // DELETE original invoice row
        mysqli_query($conn, "DELETE FROM sale_invoice WHERE id='{$item['id']}'");
    }

    // Update client total balance
    mysqli_query($conn, "UPDATE client SET total_amount = total_amount - {$grand_total} WHERE id='{$client_id}'");

    // Insert client transaction for return
    mysqli_query($conn, "
        INSERT INTO client_transactions 
            (invoice_no, client_id, total_amount, debit_amount, credit_amount, cash_mode, tdate)
        VALUES 
            ('{$next_return}', '{$client_id}', (SELECT total_amount FROM client WHERE id='{$client_id}'), 0, '{$grand_total}', 1, NOW())
    ");

    echo "<script>alert('✅ Invoice returned successfully'); window.location.href='client_invoice_report.php';</script>";
    exit;
}
?>
