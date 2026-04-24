<?php
include 'dbb.php';

if (isset($_POST['invoice_no']) && isset($_POST['items'])) {
    $invoice_no = mysqli_real_escape_string($conn, $_POST['invoice_no']);
    $items = json_decode($_POST['items'], true);

    if (empty($items)) {
        echo "No items to process!";
        exit;
    }

    mysqli_begin_transaction($conn);

    try {
        foreach ($items as $item) {
            $title = mysqli_real_escape_string($conn, $item['title']);
            $qty   = (float)$item['qty'];
            $price = (float)$item['price'];
            $net_price = isset($item['net_price']) ? (float)$item['net_price'] : $price; // ✅ use net_price if provided
            $total = $qty * $net_price;

            // ✅ Get corresponding book id
            $bookQuery = mysqli_query($conn, "SELECT id FROM books WHERE title = '$title' LIMIT 1");
            $bookRow = mysqli_fetch_assoc($bookQuery);
            $book_id = $bookRow ? $bookRow['id'] : null;

            if (!$book_id) {
                throw new Exception("Book not found for title: $title");
            }

            // ✅ Insert into sales_return
            $insertReturn = "
                INSERT INTO sales_return (invoice_no, title, qty, price, net_price, total, return_date)
                VALUES ('$invoice_no', '$title', '$qty', '$price', '$net_price', '$total', NOW())
            ";
            if (!mysqli_query($conn, $insertReturn)) {
                throw new Exception("Failed to insert sales return for $title: " . mysqli_error($conn));
            }

            // ✅ Update sale_invoice (reduce sold quantity and totals)
            $updateInvoice = "
                UPDATE sale_invoice
                SET 
                    quantity = quantity - $qty,
                    total_price = (quantity - $qty) * price,
                    net_price = (quantity - $qty) * net_price
                WHERE invoice_no = '$invoice_no' AND book_id = '$book_id'
            ";
            if (!mysqli_query($conn, $updateInvoice)) {
                throw new Exception("Failed to update sale_invoice for $title: " . mysqli_error($conn));
            }

            // ✅ Update books stock (add returned quantity back)
            $updateStock = "
                UPDATE books
                SET quantity = quantity + $qty
                WHERE id = '$book_id'
            ";
            if (!mysqli_query($conn, $updateStock)) {
                throw new Exception("Failed to update books stock for $title: " . mysqli_error($conn));
            }
        }

        mysqli_commit($conn);
        echo "✅ Sales return saved successfully!";

    } catch (Exception $e) {
        mysqli_rollback($conn);
        echo "❌ Error: " . $e->getMessage();
    }
}
?>
