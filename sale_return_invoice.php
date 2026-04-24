<?php
include 'topheader.php';
include 'dbb.php';

if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Return ID not found!</div>";
    exit;
}

$id = intval($_GET['id']);

$sql = "SELECT r.return_no, r.return_date, r.quantity, r.price, r.discount, 
               r.discount_type, r.total_price, r.net_price,
               b.title AS book_title, c.company_name AS client_name
        FROM sale_return_invoice r
        LEFT JOIN client c ON r.client_id = c.id
        LEFT JOIN books b ON r.book_id = b.id
        WHERE r.return_no = $id";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Return Invoice not found!</div>";
    exit;
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
$invoice = $rows[0];
?>

<div class="container my-5">
  <div class="card shadow-lg border-0 rounded-4 p-4">
    <div class="row mb-4 align-items-center">
      <div class="col-md-6">
        <h2 class="text-primary fw-bold mb-1">SALE RETURN INVOICE</h2>
        <p class="fw-semibold mb-1"><?= htmlspecialchars($invoice['client_name']) ?></p>
      </div>
      <div class="col-md-6 text-end">
        <p><strong>Date:</strong> <?= date('d M Y', strtotime($invoice['return_date'])) ?></p>
        <p><strong>Return No:</strong> <?= $invoice['return_no'] ?></p>
      </div>
    </div>

    <hr>

    <div class="table-responsive mb-4">
      <table class="table table-bordered text-center align-middle table-hover shadow-sm">
        <thead class="table-danger">
          <tr>
            <th>Sr</th>
            <th>Book</th>
            <th>Price</th>
            <th>Returned Qty</th>
            <th>Total</th>
            <th>Discount</th>
            <th>Net</th>
          </tr>
        </thead>
        <tbody>
          <?php
          $grand_total = 0; $serial = 1;
          foreach ($rows as $row):
              $grand_total += $row['net_price'];
          ?>
          <tr>
            <td><?= $serial++ ?></td>
            <td><?= htmlspecialchars($row['book_title']) ?></td>
            <td><?= number_format($row['price'], 2) ?></td>
            <td><?= number_format($row['quantity'], 0) ?></td>
            <td><?= number_format($row['total_price'], 2) ?></td>
            <td>
<?= number_format($row['discount'], 2) ?>
<?= ((string)$row['discount_type'] === '1' || (int)$row['discount_type'] === 1) ? '%' : 'Rs' ?>
              
</td>

            <td><?= number_format($row['net_price'], 2) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr class="table-light">
            <th colspan="6" class="text-end">Grand Total</th>
            <th><?= number_format($grand_total, 2) ?></th>
          </tr>
        </tfoot>
      </table>
    </div>

    <div class="text-center mt-4">
      <button class="btn btn-primary" onclick="window.print()">
        <i class="bi bi-printer me-1"></i> Print Return Invoice
      </button>
    </div>
  </div>
</div>
