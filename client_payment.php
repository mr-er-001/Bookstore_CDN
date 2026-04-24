<?php
include 'topheader.php';
include 'dbb.php';

$success_message = $error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $client_id      = $_POST['client_id'] ?? '';
    $client_name    = $_POST['client'] ?? '';
    $ref            = $_POST['ref'] ?? '';

    // ✅ Generate next invoice number automatically
    $invoice_sql = "SELECT MAX(invoice_no) AS last_invoice FROM client_transactions";
    $result_invoice = $conn->query($invoice_sql);

    if ($result_invoice && $row = $result_invoice->fetch_assoc()) {
        $last_invoice = (int)($row['last_invoice'] ?? 0);
        $invoice_no = $last_invoice + 1;
    } else {
        $invoice_no = 1; // start from 1 if table is empty
    }

    $amount          = $_POST['amount'] ?? '';
    $payment_method  = $_POST['payment_method'] ?? null;

    // ✅ Numeric IDs for transaction_mode
    $transection_mode = match($payment_method) {
        'Cash'            => 1,
        'Check'           => 2,
        'Draft'           => 3,
        'Online Transfer' => 4,
        'Return'          => 5,
        default           => null
    };

    // ✅ Cash mode (1 for cash, NULL otherwise)
    $cash_mode = ($payment_method === 'Cash') ? 1 : null;

    // Optional fields (store NULL instead of empty)
    $check_no        = !empty($_POST['check_no']) ? $_POST['check_no'] : null;
    $bank_name       = !empty($_POST['bank_name']) ? $_POST['bank_name'] : null;
    $transaction_ref = !empty($_POST['transaction_id']) ? $_POST['transaction_id'] : null;

    if ($client_id && $amount > 0) {
        $conn->begin_transaction();
        try {
            $transaction_id = uniqid("CT");
            $today = date("Y-m-d");

            // ✅ Client payment = credit (reduces balance)
            $debit_amount  = 0;
            $credit_amount = $amount;

            // ✅ Fetch previous balance
            $balance_sql = "SELECT total_amount FROM client WHERE id = ?";
            $balance_stmt = $conn->prepare($balance_sql);
            $balance_stmt->bind_param("i", $client_id);
            $balance_stmt->execute();
            $balance_result = $balance_stmt->get_result();
            $previous_total = $balance_result->fetch_assoc()['total_amount'] ?? 0;

            // ✅ Calculate new total (reduce balance)
            $new_total = $previous_total - $credit_amount;

            // ✅ Insert client transaction
            $sql = "INSERT INTO client_transactions 
                    (client_id, total_amount, debit_amount, credit_amount, cash_mode, transection_mode, tdate, chk_no, bank_name, transaction_id, invoice_no) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(
                "iddddisssii",
                $client_id,
                $new_total,
                $debit_amount,
                $credit_amount,
                $cash_mode,
                $transection_mode,
                $today,
                $check_no,
                $bank_name,
                $transaction_ref,
                $invoice_no
            );

            if (!$stmt->execute()) throw new Exception("Failed to insert transaction: " . $stmt->error);

            // ✅ Update client balance
            $update_sql = "UPDATE client SET total_amount = ? WHERE id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("di", $new_total, $client_id);
            if (!$update_stmt->execute()) throw new Exception("Failed to update client balance: " . $update_stmt->error);

            $conn->commit();

            $success_message = "✅ Client payment saved successfully! 
            Invoice #: {$invoice_no} | Remaining balance: Rs/ " . number_format($new_total, 2);

        } catch (Exception $e) {
            $conn->rollback();
            $error_message = "❌ Error: " . $e->getMessage();
        }
    } else {
        $error_message = "❌ Please select a client and enter a valid amount.";
    }
}
?>


<style>
/* Default state before selecting */
.btn-outline-primary {
    color: #045E70 !important;
    border-color: #045E70 !important;
    border-radius: 6px !important; /* Slightly more rounded */
}

/* Hover effect */
.btn-outline-primary:hover {
    background-color: #045E70 !important;
    color: #fff !important;
    border-color: #045E70 !important;
    border-radius: 6px !important; /* Slightly more rounded */
}

/* Active/selected radio */
.btn-check:checked + .btn-outline-primary {
    background-color: #045E70 !important;
    color: #fff !important;
    border-color: #045E70 !important;
    border-radius: 6px !important; /* Slightly more rounded */
}
</style>


<style>
/* Form inputs */
input.form-control, select.form-control, textarea.form-control {
    border: 1px solid #045E70 !important;
    border-radius: 4px !important;
}
#balance {
    border: 1px solid #045E70 !important;
    padding: 0.5rem;
    border-radius: 0.25rem;
}

/* Focus effect */
input.form-control:focus, select.form-control:focus, textarea.form-control:focus {
    border-color: #045E70 !important;
    box-shadow: 0 0 0 0.25rem rgba(4,94,112,.25);
}

/* Selected payment button */
.btn-check:checked + .btn {
    background-color: #045E70;
    color: white;
    border-color: #045E70;
}
</style>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card p-4 shadow-sm" style="border-radius: 14px !important;">
            <h3 class="text-center mb-4 fw-bold" style="color: #045E70;">Client Payment</h3>

            <?php if ($success_message): ?>
                <div class="alert alert-success text-center"><?php echo $success_message; ?></div>
            <?php elseif ($error_message): ?>
                <div class="alert alert-danger text-center"><?php echo $error_message; ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="row g-3">
                    <!-- Client Search -->
                    <div class="col-md-6 position-relative">
                        <label for="client" class="form-label fw-semibold" style="color: #045E70;">Search Client</label>
                        <input type="text" class="form-control" id="client" name="client" placeholder="Enter Client Name" autocomplete="off">
                        <input type="hidden" id="client_id" name="client_id">
                        <div id="clientResults" class="list-group position-absolute w-100 shadow" style="z-index:1000; display:none;"></div>
                    </div>

                    <!-- Reference Number -->
                    <div class="col-md-6">
                        <label for="ref" class="form-label fw-semibold" style="color: #045E70;">Reference #</label>
                        <input type="text" class="form-control" id="ref" name="ref" 
       value="<?php echo htmlspecialchars($invoice_no ?? ''); ?>" readonly>
                    </div>

                    <!-- Current Balance -->
                    <div class="col-md-6">
                        <label class="form-label fw-semibold" style="color: #045E70;">Current Balance</label>
                        <div id="balance" class="border p-2 rounded">Rs/</div>
                    </div>

                    <!-- Amount -->
                    <div class="col-md-6">
                        <label for="amount" class="form-label fw-semibold" style="color: #045E70;">Amount</label>
                        <input type="number" class="form-control" id="amount" name="amount" placeholder="Receivable Amount" step="0.01" min="0">
                    </div>
                </div>

                <!-- Payment Options -->
                <div class="mb-3 mt-3">
                    <label class="form-label fw-semibold" style="color: #045E70;">Payment Through</label>
                    <div class="d-grid gap-2 d-md-flex">
                        <input type="radio" class="btn-check" name="payment_method" id="cash" value="Cash" checked>
                        <label class="btn btn-outline-primary flex-fill text-center" for="cash">Cash</label>

                        <input type="radio" class="btn-check" name="payment_method" id="check" value="Check">
                        <label class="btn btn-outline-primary flex-fill text-center" for="check">Check</label>

                        <input type="radio" class="btn-check" name="payment_method" id="draft" value="Draft">
                        <label class="btn btn-outline-primary flex-fill text-center" for="draft">Draft</label>

                        <input type="radio" class="btn-check" name="payment_method" id="online" value="Online Transfer">
                        <label class="btn btn-outline-primary flex-fill text-center" for="online">Online Transfer</label>

                        <input type="radio" class="btn-check" name="payment_method" id="return" value="Return">
                        <label class="btn btn-outline-primary flex-fill text-center" for="return">Return</label>
                    </div>
                </div>

                <!-- Extra Fields -->
                <div id="extraFields" class="row g-3 mb-3"></div>

                <button type="submit" class="btn w-20 py-2" style="background-color: #045E70;color:white;">
                    <i class="fas fa-save me-2"></i>Save Payment
                </button>
            </form>
        </div>
    </div>
</div>

<script>
// Client search
const clientInput = document.getElementById('client');
const clientResults = document.getElementById('clientResults');
const clientId = document.getElementById('client_id');
const ref = document.getElementById('ref');
const balance = document.getElementById('balance');

clientInput.addEventListener('input', () => {
    clientId.value = '';
    ref.value = '';
    balance.textContent = 'Rs/';
    const q = clientInput.value.trim();
    if (!q) { clientResults.style.display='none'; return; }

    fetch('clientacc_search.php?q=' + encodeURIComponent(q))
        .then(res => res.json())
        .then(data => {
            clientResults.innerHTML = '';
            if (data.length === 0) {
                clientResults.innerHTML = '<div class="list-group-item text-muted">No clients found</div>';
            } else {
                data.forEach(c => {
                    const div = document.createElement('div');
                    div.className = 'list-group-item list-group-item-action client-item';
                    div.textContent = c.company_name;
                    div.dataset.id = c.id;
                    div.dataset.ref = c.ref_no || '';
                    div.dataset.balance = c.balance || '0.00';
                    clientResults.appendChild(div);
                });
            }
            clientResults.style.display = 'block';
        }).catch(err => {
            console.error(err);
            clientResults.innerHTML = '<div class="list-group-item text-danger">Search error occurred</div>';
            clientResults.style.display = 'block';
        });
});

clientResults.addEventListener('click', e => {
    const item = e.target.closest('.client-item');
    if (!item) return;
    clientInput.value = item.textContent;
    clientId.value = item.dataset.id;
    ref.value = item.dataset.ref;
    balance.textContent = `Rs/ ${item.dataset.balance || '0.00'}`;
    clientResults.style.display='none';
});

document.addEventListener('click', e => {
    if (!clientResults.contains(e.target) && e.target!==clientInput) clientResults.style.display='none';
});

// Extra fields
const extraFields = document.getElementById('extraFields');
const paymentRadios = document.querySelectorAll('input[name="payment_method"]');

function updateExtraFields(method){
    extraFields.innerHTML='';
    if(['Check','Draft','Online Transfer'].includes(method)){
        const row = document.createElement('div'); row.className='row g-3';
        const col1 = document.createElement('div'); col1.className='col-md-6';
        if(method==='Check') col1.innerHTML=`<label class="form-label fw-semibold" style="color:#045E70;">Check No</label><input type="text" class="form-control" name="check_no" placeholder="Enter Check Number">`;
        else if(method==='Draft') col1.innerHTML=`<label class="form-label fw-semibold" style="color:#045E70;">Draft No</label><input type="text" class="form-control" name="check_no" placeholder="Enter Draft Number">`;
        else col1.innerHTML=`<label class="form-label fw-semibold" style="color:#045E70;">Transaction ID</label><input type="text" class="form-control" name="transaction_id" placeholder="Enter Transaction ID">`;
        const col2 = document.createElement('div'); col2.className='col-md-6';
        col2.innerHTML=`<label class="form-label fw-semibold" style="color:#045E70;">Bank Name</label><input type="text" class="form-control" name="bank_name" placeholder="Enter Bank Name">`;
        row.appendChild(col1); row.appendChild(col2);
        extraFields.appendChild(row);
    }
}
paymentRadios.forEach(r=>r.addEventListener('change',()=>updateExtraFields(r.value)));
updateExtraFields(document.querySelector('input[name="payment_method"]:checked')?.value);
</script>

<?php include 'footer.php'; ?>
