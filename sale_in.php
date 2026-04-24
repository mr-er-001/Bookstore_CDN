<?php
// Enable error reporting
error_reporting(E_ALL);       // Report all PHP errors
ini_set('display_errors', 1); // Display errors on the browser
ini_set('display_startup_errors', 1); // Show errors during PHP startup
?>

<?php
include 'topheader.php';
include 'dbb.php';

// Check if invoice_no is provided
$invoice_no = trim($_GET['invoice_no'] ?? '');
if (!$invoice_no) {
    echo "<div class='alert alert-danger'>Invoice not found!</div>";
    exit;
}

// Fetch all items for this invoice
$sql = "
    SELECT si.id, si.invoice_no, si.invoice_date, si.price, si.quantity, 
           si.discount, si.discount_type, 
           b.title AS book_title, 
           c.company_name AS client_name
    FROM sale_invoice si
    LEFT JOIN books b ON si.book_id = b.id
    LEFT JOIN client c ON si.client_id = c.id
    WHERE TRIM(si.invoice_no) = ?
    ORDER BY si.id ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $invoice_no);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Invoice not found!</div>";
    exit;
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $total = $row['price'] * $row['quantity'];
    if ($row['discount_type'] == 1) {
        $discount_value = ($row['discount'] / 100) * $total;
        $discount_label = '%';
    } else {
        $discount_value = $row['discount'];
        $discount_label = 'Rs';
    }
    $row['total_price'] = $total;
    $row['net_price'] = $total - $discount_value;
    $row['discount_label'] = $discount_label;
    $rows[] = $row;
}

// First row for invoice header
$invoice = $rows[0];
?>

<div class="container my-5">
  <div class="card shadow-lg border-0 rounded-4 p-4">
    
    <!-- Header -->
    <div class="row mb-4 align-items-center">
      <div class="col-md-6">
        <h2 class="text-primary fw-bold mb-1">SALE INVOICE</h2>
        <p class="fw-semibold mb-1"><?= htmlspecialchars($invoice['client_name']) ?></p>
      </div>
      <div class="col-md-6 text-end">
        <p class="mb-1"><strong>Date:</strong> <?= date('d M Y', strtotime($invoice['invoice_date'])) ?></p>
        <p class="mb-1"><strong>Invoice No:</strong> <?= htmlspecialchars($invoice['invoice_no']) ?></p>
      </div>
    </div>

    <hr>

    <!-- Invoice Table -->
    <div class="table-responsive mb-4">
      <table class="table table-bordered text-center align-middle table-hover shadow-sm">
        <thead class="table-primary">
          <tr>
            <th>Sr. No</th>
            <th>Book</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total Price</th>
            <th>Discount</th>
            <th>Net Price</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $grand_total = 0;
          $serial = 1;
          foreach ($rows as $row):
              $grand_total += $row['net_price'];
          ?>
          <tr>
            <td><?= $serial++ ?></td>
            <td><?= htmlspecialchars($row['book_title']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= number_format($row['quantity'], 0) ?></td>
            <td><?= number_format($row['total_price'], 2) ?></td>
            <td><?= number_format($row['discount'], 2) . ' ' . $row['discount_label'] ?></td>
            <td class="fw-bold"><?= number_format($row['net_price'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="table-light">
            <th colspan="6" class="text-end">Grand Total</th>
            <th class="fw-bold"><?= number_format($grand_total, 2) ?></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="text-center mt-4">
      <button class="btn btn-success" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> Print Invoice
      </button>
    </div>
  </div>
</div>

<style>
@media print {
  body * { visibility: hidden; }
  .card, .card * { visibility: visible; }
  .card { position: absolute; left: 0; top: 0; width: 100%; }
  .btn { display: none; }
}
</style>

<?php include 'footer.php'; ?>
