<?php include 'topheader.php'; ?>
<style>
    /* Make the body a flex container */
body {
    display: flex;
    flex-direction: column;
    min-height: 100vh; /* full viewport height */
    margin: 0;
}

/* Make the main content grow to fill space */
main {
    flex: 1;
}

</style>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class=" border rounded p-3 mb-4" style="background-color: #045E70;color:white;">
                <h4 class="text-center mb-0" style="background-color: #045E70;color:white; padding: 12px; font-weight: 600; ">
                    SALES BY CLIENT AND INVOICE #
                </h4>
            </div>

            <!-- Search Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-10">
                    <form id="clientInvoiceForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">

                       <!-- Client Search -->
<div class="d-flex flex-column position-relative" style="min-width: 220px;">
    <label class="form-label fw-semibold mb-1">Client</label>
    
    <!-- Input for typing client name -->
    <input type="text" id="clientSearch" class="form-control search-client" placeholder="Type client name">
<input type="hidden" id="clientId" class="client-id" name="client_id">
  
    <!-- Suggestions dropdown -->
    <div class="list-group clientResults position-absolute w-100 shadow" 
         style="z-index:1000; max-height:200px; overflow-y:auto; display:none; top:100%; left:0; font-size:13px;">
    </div>
</div>


                        <!-- Invoice -->
                        <div class="d-flex flex-column" style="min-width: 180px;">
                            <label class="form-label fw-semibold mb-1">Invoice #</label>
                            <input type="text" id="invoiceNo" class="form-control" placeholder="Type invoice #">
                        </div>

                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn px-4" style="background-color: #045E70;color:white;">Search</button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="background-color: #045E70;color:white;">Invoice #</th>
                            <th style="background-color: #045E70;color:white;">Title</th>
                            <th style="background-color: #045E70;color:white;">Sale Date</th>
                            <th style="background-color: #045E70;color:white;">Price</th>
                            <th style="background-color: #045E70;color:white;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="salesTableBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-lg mb-2 d-block"></i>
                                Select client and enter invoice # to view sales
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Print Button -->
            <div class="text-end mt-3" id="printButtonContainer" style="display:none;">
                <button class="btn btn-success" id="printButton">
                    <i class="fas fa-print me-1"></i> Print
                </button>
            </div>

        </div>
    </div>
</div>

<script>
const clientSearch = document.getElementById('clientSearch'),
      clientIdInput = document.getElementById('clientId'),
      invoiceNoInput = document.getElementById('invoiceNo'),
      clientSuggestions = document.getElementById('clientSuggestions'),
      clientInvoiceForm = document.getElementById('clientInvoiceForm'),
      salesTableBody = document.getElementById('salesTableBody'),
      printButtonContainer = document.getElementById('printButtonContainer'),
      printButton = document.getElementById('printButton');

// --- Autocomplete ---
clientSearch.addEventListener('input', () => {
    const query = clientSearch.value.trim();
    clientIdInput.value = "";
    if(!query) {
        clientSuggestions.style.display = "none";
        return;
    }

    fetch('fetch_client_suggestions_ajax.php?q=' + encodeURIComponent(query))
        .then(r => r.text())
        .then(html => {
            clientSuggestions.innerHTML = html;
            clientSuggestions.style.display = html.trim() ? "block" : "none";

            document.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', function(){
                    clientSearch.value = this.dataset.name;
                    clientIdInput.value = this.dataset.id;
                    clientSuggestions.style.display = "none";
                });
            });
        });
});

// Hide suggestions on outside click
document.addEventListener('click', e => {
    if(!clientSearch.contains(e.target) && !clientSuggestions.contains(e.target)){
        clientSuggestions.style.display = "none";
    }
});

// --- Fetch sales ---
// --- Fetch sales ---
clientInvoiceForm.addEventListener('submit', e => {
    e.preventDefault();

    const clientId = clientIdInput.value.trim();
const invoiceNo = invoiceNoInput.value.trim();

// At least one must be filled
if (!clientId && !invoiceNo) {
    alert("Please select a client or enter an invoice number.");
    return;
}

const queryParams = new URLSearchParams();
if (clientId) queryParams.append("client", clientId);
if (invoiceNo) queryParams.append("invoice", invoiceNo);

fetch(`fetch_client_invoice_report.php?${queryParams.toString()}`)

        .then(r => r.text())
        .then(html => {
            salesTableBody.innerHTML = html;
            printButtonContainer.style.display = 
                html.includes('No sales found') || html.includes('Invalid') 
                ? 'none' 
                : 'block';
        })
        .catch(() => {
            salesTableBody.innerHTML = '<tr><td colspan="4" class="text-center text-danger">Error loading sales</td></tr>';
            printButtonContainer.style.display = 'none';
        });
});



// --- Print ---
printButton.addEventListener('click', () => {
    const tableHTML = document.querySelector('.table').outerHTML;
    const w = window.open('', '', 'width=900,height=700');
    w.document.write(`<html><head><title>Client Sales</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        </head><body>
        <h3 class="text-center mb-3">Client Sales - ${clientSearch.value} (Invoice: ${invoiceNoInput.value})</h3>${tableHTML}</body></html>`);
    w.document.close();
    w.print();
});
</script>
<?php include 'footer.php'; ?>
