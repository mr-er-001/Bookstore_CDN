<?php
include 'dbb.php'; // database connection

if (isset($_POST['invoice_no'])) {
    $invoice_no = mysqli_real_escape_string($conn, $_POST['invoice_no']);

    $sql = "
        SELECT 
            si.invoice_no, 
            b.title, 
            si.quantity, 
            si.invoice_date, 
            si.price, 
            si.discount,
            si.discount_type,   -- ✅ Fetch discount type
            si.net_price,       
            si.total_price      
        FROM sale_invoice si
        JOIN books b ON si.book_id = b.id
        WHERE si.invoice_no = '$invoice_no'
    ";

    $result = mysqli_query($conn, $sql);

    if (!$result) {
        echo "<tr><td colspan='8' style='color:red;'>SQL Error: " . mysqli_error($conn) . "</td></tr>";
        exit;
    }

    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {

            $price     = (float)$row['price'];
            $discount  = (float)$row['discount'];
            $disc_type = $row['discount_type'] ?? 'percent'; // default fallback
            $net_price = (float)$row['net_price'];
            $total     = (float)$row['total_price'];
            $qty       = (float)$row['quantity'];

            // ✅ Calculate if missing
            if ($total <= 0) {
                $total = $price * $qty;
            }

            if ($net_price <= 0) {
                if ($disc_type === 'percent') {
                    $net_price = $total - ($total * ($discount / 100));
                } else {
                    $net_price = $total - $discount;
                }
            }

            // ✅ Format discount display
            $discount_display = $disc_type === 'percent' 
                ? number_format($discount, 2) . '%' 
                : 'Rs ' . number_format($discount, 2);

            echo "
                <tr>
                    <td>{$row['invoice_no']}</td>
                    <td>{$row['title']}</td>
                     
                    <td>
                        <input type='number' 
                               class='form-control qty-input text-center' 
                               value='{$row['quantity']}' 
                               min='0' 
                               max='{$row['quantity']}' 
                               data-max='{$row['quantity']}'
                               style='width:80px;'>
                    </td>
                    <td>{$row['quantity']}</td>
                    <td class='price'>".number_format($price, 2)."</td>
                    <td class='discount'>{$discount_display}</td>
                    <td class='net-price'>".number_format($net_price, 2)."</td>
                    <td class='price-val'>".number_format($total, 2)."</td>
                </tr>
            ";
        }
    } else {
        echo "
            <tr>
                <td colspan='8' class='text-muted py-3'>
                    <i class='bi bi-info-circle'></i> No records found for Invoice #{$invoice_no}
                </td>
            </tr>
        ";
    }
}
?>
  