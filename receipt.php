<?php
include 'topheader.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invoice ID not found!</div>";
    exit;
}

$invoice_id = intval($_GET['id']); // Unique row ID

// Fetch all rows belonging to this invoice by invoice_no of the first row
$sql_first = "SELECT invoice_no FROM purchase_invoice WHERE invoice_no = $invoice_id LIMIT 1";
$res_first = $conn->query($sql_first);

if ($res_first->num_rows == 0) {
    echo "<div class='alert alert-danger'>Invoice not found!</div>";
    exit;
}

$invoice_no = $res_first->fetch_assoc()['invoice_no'];

// Fetch all rows for this invoice
$sql = "SELECT p.id, p.invoice_no, p.invoice_date, p.price, p.quantity, p.discount, p.discount_type, p.total_price, p.net_price,
               v.company_name AS vendor_name, v.postal_address AS vendor_address, v.phone AS vendor_phone, v.email AS vendor_email,
               b.title AS book_title
        FROM purchase_invoice p
        LEFT JOIN vendor v ON p.vendor_id = v.id
        LEFT JOIN books b ON p.book_id = b.id
        WHERE p.invoice_no = '$invoice_no'
        ORDER BY p.id ASC";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Invoice not found!</div>";
    exit;
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    // Calculate total price if not set
    $row['total_price'] = $row['total_price'] ?: $row['price'] * $row['quantity'];

    // Normalize discount type as integer
    // Normalize discount type as integer
$discount_type = (int)$row['discount_type']; // 1 = percent, 0 = cash

if ($discount_type === 1) {
    // Percent discount
    $row['net_price'] = $row['total_price'] - ($row['total_price'] * $row['discount'] / 100);
    $row['discountSign'] = '%';
} else {
    // Cash discount
    $row['net_price'] = $row['total_price'] - $row['discount'];
    $row['discountSign'] = '₨';
}


    $rows[] = $row;
}


$invoice = $rows[0]; // Use first row for header
?>

<div class="container my-5">
  <div class="card shadow-lg border-0 rounded-4 p-4" style="font-family: 'Segoe UI', sans-serif;">

    <!-- Header -->
    <div class="row mb-4 align-items-center">
      <div class="col-md-6">
        <h2 class="text-primary fw-bold mb-1">PURCHASE INVOICE</h2>
        <p class="fw-semibold mb-1"><?= htmlspecialchars($invoice['vendor_name']) ?></p>
        <p class="mb-1 text-muted"><i class="bi bi-geo-alt me-1"></i><?= $invoice['vendor_address'] ?></p>
        <p class="mb-1 text-muted"><i class="bi bi-telephone me-1"></i><?= $invoice['vendor_phone'] ?></p>
        <p class="mb-1 text-muted"><i class="bi bi-envelope me-1"></i><?= $invoice['vendor_email'] ?></p>
      </div>
      <div class="col-md-6 text-end">
        <p class="mb-1"><strong>Date:</strong> <?= date('d M Y', strtotime($invoice['invoice_date'])) ?></p>
        <p class="mb-1"><strong>Invoice No:</strong> <?= $invoice['invoice_no'] ?></p>
        <p class="mb-1"><strong>Invoice ID:</strong> <?= $invoice['id'] ?></p>
      </div>
    </div>

    <hr>

    <!-- Invoice Table -->
    <div class="table-responsive mb-4">
      <table class="table table-bordered text-center align-middle table-hover shadow-sm">
        <thead class="table-primary">
          <tr>
            <th>Sr. No</th>
            <th>Book Name</th>
            <th>Price (Rs.)</th>
            <th>Quantity</th>
            <th>Discount</th>
            <th>Total Price</th>
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
  <td><?= $row['quantity'] ?></td>
  <td><?= number_format($row['discount'], 2) ?> <?= $row['discountSign'] ?></td>
  <td><?= number_format($row['total_price'], 2) ?></td>
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
