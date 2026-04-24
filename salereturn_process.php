<?php
include 'dbb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['invoice_no'])) {
    $invoice_no = $_POST['invoice_no'];

    // Fetch all items for this invoice
    $sales = mysqli_query($conn, "SELECT * FROM sale_invoice WHERE invoice_no='{$invoice_no}'");
    if(mysqli_num_rows($sales) == 0){
        die('No sales found for this invoice');
    }

    $next_return_row = mysqli_fetch_row(mysqli_query($conn, "SELECT MAX(return_no) FROM sale_return_invoice"));
    $next_return = ($next_return_row[0]) ? ($next_return_row[0] + 1) : 1;

    $grand_total = 0;

    while($sale = mysqli_fetch_assoc($sales)){
        $book_id = $sale['book_id'];
        $qty = $sale['quantity'];
        $price = $sale['price'];
        $discount = $sale['discount'];
        $discount_type = (int)$sale['discount_type'];

        $total_price = $price * $qty;
        $net_price = ($discount_type === 1) ? $total_price - ($total_price * $discount / 100) : $total_price - $discount;
        if($net_price < 0) $net_price = 0;

        $grand_total += $net_price;

        // Update sale_invoice: mark as returned
        mysqli_query($conn, "UPDATE sale_invoice SET quantity=0, net_price=0, status='returned' WHERE id='{$sale['id']}'");

        // Return stock
        mysqli_query($conn, "UPDATE books SET quantity = quantity + {$qty} WHERE id='{$book_id}'");

        // Insert into return invoice
        mysqli_query($conn, "
            INSERT INTO sale_return_invoice
            (return_no, invoice_no, client_id, book_id, quantity, price, discount, discount_type, total_price, net_price, return_date)
            VALUES
            ('{$next_return}', '{$invoice_no}', '{$sale['client_id']}', '{$book_id}', '{$qty}', '{$price}', '{$discount}', '{$discount_type}', '{$total_price}', '{$net_price}', NOW())
        ");
    }

    // Update client total_amount
    $client_id = mysqli_fetch_assoc(mysqli_query($conn, "SELECT client_id FROM sale_invoice WHERE invoice_no='{$invoice_no}'"))['client_id'];
    mysqli_query($conn, "UPDATE client SET total_amount = total_amount - {$grand_total} WHERE id='{$client_id}'");

    // Insert transaction
    mysqli_query($conn, "
        INSERT INTO client_transactions
        (invoice_no, client_id, total_amount, debit_amount, credit_amount, cash_mode, tdate)
        VALUES
        ('{$next_return}', '{$client_id}', (SELECT total_amount FROM client WHERE id='{$client_id}'), 0, {$grand_total}, 1, NOW())
    ");

    // Redirect to return invoice
    header("Location: ./sale_return_invoice.php?id={$next_return}");
    exit;
}
?>
