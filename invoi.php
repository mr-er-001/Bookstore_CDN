<?php
include 'topheader.php';
include 'dbb.php';

if (empty($_GET['invoice_no'])) {
    echo "<div class='alert alert-danger'>Invoice not found!</div>";
    exit;
}

$invoice_no = trim($_GET['invoice_no']);

// Fetch all books for this purchase invoice
$sql = "
    SELECT pi.id, pi.invoice_no, pi.invoice_date, pi.price, pi.quantity, 
           b.title AS book_title, v.company_name AS vendor_name
    FROM purchase_invoice pi
    LEFT JOIN books b ON pi.book_id = b.id
    LEFT JOIN vendor v ON pi.vendor_id = v.id
    WHERE TRIM(pi.invoice_no) = ?
    ORDER BY pi.id ASC
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
    $row['total_price'] = $total;
    $rows[] = $row;
}

// Use first row for header info
$invoice = $rows[0];
?>

<div class="container my-5">
  <div class="card shadow-lg border-0 rounded-4 p-4">
    <div class="row mb-4 align-items-center">
      <div class="col-md-6">
        <h2 class="text-primary fw-bold mb-1">PURCHASE INVOICE</h2>
        <p class="fw-semibold mb-1"><?= htmlspecialchars($invoice['vendor_name']) ?></p>
      </div>
      <div class="col-md-6 text-end">
        <p class="mb-1"><strong>Date:</strong> <?= date('d M Y', strtotime($invoice['invoice_date'])) ?></p>
        <p class="mb-1"><strong>Invoice No:</strong> <?= $invoice['invoice_no'] ?></p>
      </div>
    </div>

    <hr>

    <div class="table-responsive mb-4">
      <table class="table table-bordered text-center align-middle table-hover shadow-sm">
        <thead class="table-primary">
          <tr>
            <th>Sr. No</th>
            <th>Book</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total Price</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $grand_total = 0;
          $serial = 1;
          foreach ($rows as $row):
              $grand_total += $row['total_price'];
          ?>
          <tr>
            <td><?= $serial++ ?></td>
            <td><?= htmlspecialchars($row['book_title']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= number_format($row['quantity'], 0) ?></td>
            <td class="fw-bold"><?= number_format($row['total_price'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="table-light">
            <th colspan="4" class="text-end">Grand Total</th>
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
