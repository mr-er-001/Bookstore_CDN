<?php include 'topheader.php'; ?>
<style>
/* Make the body a flex container */
body {
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    margin: 0;
}

/* Make main content grow to fill space */
main {
    flex: 1;
}

/* Date picker width */
.date-picker {
    max-width: 150px;
}
</style>

<main>
<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">

            <!-- Header -->
            <div class="border p-3 mb-3" style="background-color: #045E70; color:white;">
                <h4 class="text-center mb-0" style="color:white;">SALE REPORT</h4>
            </div>

            <!-- Date Filter Form -->
            <div class="mb-4 text-center">
                <form id="purchaseForm" class="d-flex justify-content-center gap-2">
                    <label class="fw-semibold mt-2">Date</label>
                    <input type="text" class="form-control date-picker" id="fromDate" 
                           placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">

                    <label class="fw-semibold mt-2">to</label>
                    <input type="text" class="form-control date-picker" id="toDate" 
                           placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">

                    <button type="submit" class="btn" style="background-color: #045E70;color:white;">Search</button>
                </form>
            </div>

            <!-- Results Table -->
            <div class="table-responsive" id="reportArea">
                <table class="table table-bordered">
                   <thead style="background:#000; color:#fff;">
                        <tr>
                            <th style="background-color: #045E70;color:white;">Invoice No</th>
                            <th style="background-color: #045E70;color:white;">Client Name</th>
                            <th style="background-color: #045E70;color:white;">Date</th>
                            <th style="background-color: #045E70;color:white;">Price</th>
                            <th style="background-color: #045E70;color:white;">Net Price</th>
                            <th style="background-color: #045E70;color:white;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseTableBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted">Select date range to view purchases</td>
                        </tr>
                    </tbody>
                </table>

                <!-- Print Button -->
                <div class="text-end mt-2">
                    <button class="btn btn-success" onclick="printReport()">
                        <i class="bi bi-printer me-1"></i> Print Report
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>
</main>

<script>
// Handle form submit
document.getElementById('purchaseForm').addEventListener('submit', function(e){
    e.preventDefault();

    let from = document.getElementById('fromDate').value;
    let to   = document.getElementById('toDate').value;

    if(!from || !to){
        alert("Please select both dates");
        return;
    }

    // Fetch data from PHP
    fetch(`fetch_sale_report.php?from=${from}&to=${to}`)
        .then(response => response.text())
        .then(data => {
            document.getElementById('purchaseTableBody').innerHTML = data;
        })
        .catch(error => console.error('Error:', error));
});

// Print only the table
function printReport() {
    const printContents = document.getElementById('reportArea').innerHTML;
    const originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();
}
</script>

<!-- Bootstrap Icons CDN (for printer icon) -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

<?php include 'footer.php'; ?>
