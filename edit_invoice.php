<?php
include 'dbb.php';
include 'topheader.php';

// Get invoice number from URL
$invoice_no = $_GET['invoice'] ?? '';

if (!$invoice_no) {
    echo "<div class='alert alert-danger text-center mt-4'>Invalid Invoice Number</div>";
    exit;
}

// Fetch invoice items (after return update)
$sql = "SELECT * FROM sales_return WHERE invoice_no = '$invoice_no'";

$result = mysqli_query($conn, $sql);
?>
<style>
body { 
    background: #e5f4f9; 
    font-family: 'Open Sans', sans-serif; 
}
.card-premium {
    border-radius: 15px;
    border: none;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    background: #ffffff;
}
.table thead {
    background-color: #045E70;
    color: #fff;
}
</style>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 style="color:#045E70;">Invoice Correction Receipt</h4>
        <button id="printBtn" class="btn btn-sm" style="background-color:#045E70;color:white;">
            <i class="bi bi-printer"></i> Print
        </button>
    </div>

    <div class="card-premium p-4">
        <div class="mb-3">
            <strong>Invoice No:</strong> <?php echo htmlspecialchars($invoice_no); ?><br>
            <strong>Date:</strong> <?php echo date('Y-m-d'); ?>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle text-center">
                <thead>
                    <tr>
                       <th>Title</th>
<th>Returned Qty</th>
<th>Price</th>
<th>Total</th>
<th>Return Date</th>

                    </tr>
                </thead>
                <tbody>
                    <?php
                    $grand_total = 0;
                    if (mysqli_num_rows($result) > 0) {
                      while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>{$row['title']}</td>
            <td>{$row['qty']}</td>
            <td>{$row['price']}</td>
            <td>".number_format($row['total'], 2)."</td>
            <td>{$row['return_date']}</td>
          </tr>";
}

                    } else {
                        echo "<tr><td colspan='4' class='text-muted'>No items found for this invoice</td></tr>";
                    }
                    ?>
                </tbody>
                <tfoot class="bg-light fw-bold">
                    <tr>
                        <td colspan="3" class="text-end">Grand Total</td>
                        <td><?php echo number_format($grand_total, 2); ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
document.getElementById("printBtn").addEventListener("click", function() {
    var content = document.querySelector(".card-premium").outerHTML;
    var w = window.open('', '', 'width=900,height=700');
    w.document.write(`
        <html>
        <head>
            <title>Invoice Correction Receipt</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        </head>
        <body>
            <div class='container mt-4'>
                <h3 class='text-center mb-3'>Invoice Correction Receipt</h3>
                ${content}
            </div>
        </body>
        </html>
    `);
    w.document.close();
    w.print();
});
</script>

<?php require_once 'footer.php'; ?>
