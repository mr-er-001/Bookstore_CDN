<?php
include 'topheader.php';
include 'dbb.php';


/* ================= AJAX HANDLER ================= */


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_payment'])) {

    $type       = $_POST['type'];    
    $invoice_no = intval($_POST['invoice_no']);
    $pub_date   = date('Y-m-d');
    $client_id  = $_POST['client_id'];

    if($type === 'full'){
        $grand_total = floatval($_POST['full_amount']);

        // Calculate client balance
        $balanceQuery = mysqli_query($conn, "SELECT COALESCE(SUM(debit_amount),0)-COALESCE(SUM(credit_amount),0) AS balance FROM client_transactions WHERE client_id='$client_id'");
        $balanceRow = mysqli_fetch_assoc($balanceQuery);
        $previous_total = $balanceRow ? $balanceRow['balance'] : 0;
        $new_total = $previous_total - $grand_total;
        // Insert transaction
        mysqli_query($conn, "INSERT INTO client_transactions (invoice_no, client_id, total_amount, debit_amount, credit_amount, tdate) VALUES ('$invoice_no','$client_id','$new_total','0','$grand_total','$pub_date')");
        $updateClient = mysqli_query($conn, "UPDATE client SET total_amount = $new_total WHERE id = '$client_id'");
        echo "FULL PAYMENT SUCCESS";
        exit;
    }

    if($type === 'partial'){
        $amount = floatval($_POST['amount']);

        $balanceQuery = mysqli_query($conn, "SELECT COALESCE(SUM(debit_amount),0)-COALESCE(SUM(credit_amount),0) AS balance FROM client_transactions WHERE client_id='$client_id'");
        $balanceRow = mysqli_fetch_assoc($balanceQuery);
        $previous_total = $balanceRow ? $balanceRow['balance'] : 0;

        $new_total = $previous_total - $amount;

        mysqli_query($conn, "INSERT INTO client_transactions (invoice_no, client_id, total_amount, debit_amount, credit_amount, tdate) VALUES ('$invoice_no','$client_id','$new_total','0','$amount','$pub_date')");
            $updateClient = mysqli_query($conn, "UPDATE client SET total_amount = $new_total WHERE id = '$client_id'");
        echo "PARTIAL PAYMENT SUCCESS";
        exit;
    }
}


if (!isset($_GET['id'])) {
    echo "<div class='alert alert-danger'>Invoice ID not found!</div>";
    exit;
}

$id = intval($_GET['id']); // Ensure it's numeric

// Fetch invoice rows with client & book details
$sql = "SELECT c.id as client_id ,s.id, s.invoice_no, s.invoice_date, s.price, s.quantity,      
               s.discount, s.discount_type, s.total_price, s.net_price, 
               b.title AS book_title, c.company_name AS client_name
        FROM sale_invoice s
        LEFT JOIN client c ON s.client_id = c.id
        LEFT JOIN books b ON s.book_id = b.id
        WHERE s.invoice_no = $id";

$result = $conn->query($sql);

if ($result->num_rows == 0) {
    echo "<div class='alert alert-danger'>Invoice not found!</div>";
    exit;
}

$rows = [];
while ($row = $result->fetch_assoc()) {
    // Ensure total_price & net_price are calculated correctly
    $total = $row['price'] * $row['quantity'];

    if ($row['discount_type'] == 1) {  // 1 = percent, 0 = cash
    $discount_value = ($row['discount'] / 100) * $total;
    $discount_label = '%';
} else {
    $discount_value = $row['discount'];
    $discount_label = 'Rs';
}


    $row['total_price'] = $total;
    $row['net_price']   = $total - $discount_value;

    $rows[] = $row;
}

// Use first row for invoice header
$invoice = $rows[0];
?>

<div class="container">
  <div class="card shadow-lg border-0 rounded-4 p-4">

    <!-- Header -->
    <div class="row mb-4 align-items-center receipt-header">

      <div class="col-md-6">
        <h2 class="text-primary fw-bold mb-1">SALE INVOICE</h2>
        <p class="fw-semibold mb-1"><?= htmlspecialchars($invoice['client_name']) ?></p>
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
    <th class="col-no">No</th>
    <th class="col-book">Book</th>
    <th class="col-price">Price</th>
    <th class="col-qty">Qty</th>
    <th class="col-total">Total</th>
    <th class="col-disc">Dis</th>
    <th class="col-net">Net</th>
</tr>
        </thead>
        <tbody>
          <?php
          $grand_total = 0;
          $serial = 1;
          foreach ($rows as $row):
    $total = $row['price'] * $row['quantity'];

    if ($row['discount_type'] == 1) { // ✅ percent
        $discount_value = ($row['discount'] / 100) * $total;
        $discount_label = '%';
    } else {
        $discount_value = $row['discount'];
        $discount_label = 'Rs';
    }

    $net_price = $total - $discount_value;
    $grand_total += $net_price;
?>
<tr>
  <td><?= $serial++ ?></td>
  <td class="col-book"><?= htmlspecialchars($row['book_title']) ?></td>
  <td><?= ($row['price'] == (int)$row['price']) ? (int)$row['price'] : number_format($row['price'], 2) ?></td>

  <td><?= number_format($row['quantity'], 0) ?></td>
 <td><?= ($total == (int)$total) ? (int)$total : number_format($total, 2) ?></td>

  <td><?= (int)$row['discount'] . ' ' . $discount_label ?></td>
  <td class="fw-bold"><?= number_format($net_price, 2) ?></td>
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
        <img src="assets/img/print.png" style="height: 52px;" class="img-btn" onclick="fetch('printthermal.php?invoice_no=<?php echo $id; ?>')">
        <img src="assets/img/paid full.png" style="height:52px; cursor:pointer;" onclick="payFull(<?= $id ?>)">
        <img src="assets/img/paid partial.png" style="height:52px; cursor:pointer;" onclick="showPartialInput()">
        <input name="invocie_no" type="hidden" value="<?=  $invoice['invoice_no'] ?>" >
        <input id="fullAmount" name="full_amount" type="hidden" value="<?= number_format($grand_total, 2) ?>" >
        <input id="client_id" name="client_id" type="hidden" value="<?= $invoice['client_id']; ?>" >
<div id="partialBox" style="display:none;  margin-top:10px;">
    <input type="number" id="partialAmount" name="partial_amount" class="form-control form-control-sm" placeholder="Enter amount" min="1" >
    <button class="btn btn-success btn-sm" onclick="payPartial(<?= $id ?>)">Submit</button>
</div>


    </div>
  </div>
</div>


<script>
    
    function payFull(invoiceNo) {
    let fullAmount = document.getElementById("fullAmount").value;
    let client_id = document.getElementById("client_id").value;

    let data = new FormData();
    data.append("ajax_payment", "1");
    data.append("invoice_no", invoiceNo);
    data.append("type", "full");
    data.append("full_amount", fullAmount);
    data.append("client_id", client_id);
    
    fetch("sale_invoice.php", { method:"POST", body:data })
.then(res => res.text())
.then(msg => {
    console.log("Server response:", msg); // see exact output
    msg = msg.trim();
    if(msg.includes("SUCCESS")){
        alert("FULL PAYMENT SUCCESS");
        window.location.href = "sale_book.php";
    } else {
        alert("Error: " + msg);
    }
});

}
function showPartialInput() {
    document.getElementById('partialBox').style.display = 'block';
}

function payPartial(invoiceNo) {
    let amount = document.getElementById("partialAmount").value;
    if(amount <= 0){
        alert("Enter valid amount");
        return;
    }
let client_id = document.getElementById("client_id").value;
    let data = new FormData();
    data.append("ajax_payment", "1");
    data.append("invoice_no", invoiceNo);
    data.append("type", "partial");
    data.append("amount", amount);
    data.append("client_id", client_id);
    
        fetch("sale_invoice.php", { method:"POST", body:data })
    .then(res=>res.text())
    .then(msg=>{
        msg = msg.trim();
        if(msg.includes("SUCCESS")){
               alert("PARTIAL PAYMENT SAVED");
           window.location.href = "sale_book.php"; // redirect to index.php
        } else {
            alert("Error: "+msg);
        }
    });
}
</script>



<?php include 'footer.php'; ?>
