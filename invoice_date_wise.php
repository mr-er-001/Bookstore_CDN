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
            <div class="border rounded p-3 mb-4" style="background-color: #045E70;color:white;">
                <h4 class="text-center mb-0" style="background-color: #045E70; color:white; padding: 12px; font-weight: 600;">
                    INVOICE BY DATE
                </h4>
            </div>

            <!-- Search Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <form id="invoiceDateForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">

                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold mb-1">From</label>
                            <input type="text" class="form-control date-picker" id="fromDate" 
       placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off" style="max-width:150px;">
<input type="hidden" id="fromDate_mysql" name="fromDate_mysql">
                        </div>

                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold mb-1">To</label>
                            <input type="text" class="form-control date-picker" id="toDate" 
       placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off" style="max-width:150px;">
<input type="hidden" id="toDate_mysql" name="toDate_mysql">
                        </div>

                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn  px-4" style="background-color: #045E70;color:white;">Search</button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive shadow-sm rounded" id="reportArea">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
    <tr>
        <th style="background-color: #045E70;color:white;">Client</th>
        <th style="background-color: #045E70;color:white;">Title</th>
        <th style="background-color: #045E70;color:white;">Quantity</th>
        <th style="background-color: #045E70;color:white;">Invoice No</th>
        <th style="background-color: #045E70;color:white;">Date</th>
        <th style="background-color: #045E70;color:white;">Price</th>
    </tr>
</thead>

                    <tbody id="invoiceDateTableBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-lg mb-2 d-block"></i>
                                Select date range to view invoices
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
const invoiceDateForm = document.getElementById('invoiceDateForm'),
      invoiceDateTableBody = document.getElementById('invoiceDateTableBody'),
      fromDate = document.getElementById('fromDate'),
      toDate = document.getElementById('toDate');

// --- Fetch report ---
invoiceDateForm.addEventListener('submit', e => {
    e.preventDefault();
    if(!fromDate.value || !toDate.value){
        alert("Please select a date range");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `fetch_invoice_date_report.php?from=${fromDate.value}&to=${toDate.value}`, true);
    xhr.onload = function(){
        if(this.status === 200){
            invoiceDateTableBody.innerHTML = this.responseText;
        }
    };
    xhr.send();
});
function printReport() {
    const printContents = document.getElementById('reportArea').innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}

</script>

<?php include 'footer.php'; ?>
