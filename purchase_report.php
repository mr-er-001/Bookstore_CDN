
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

<div class="container-fluid py-3">
    <div class="row">
        <div class="col-12">

            <!-- Header -->
            <div class="border p-3 mb-3" style="background-color: #045E70;color:white;">
                <h4 class="text-center mb-0" style="background-color: #045E70; padding: 10px; font-weight: bold; color: white;">
                    PURCHASED BOOKS BY DATE
                </h4>
            </div>

            <!-- Date Filter Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form id="dateForm" class="row g-2 align-items-center justify-content-center">
                        <div class="col-md-5">
                            <label class="form-label fw-semibold">From:</label>
                            <input type="text" class="form-control date-picker" id="fromDate" 
                                placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
                        </div>

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">To:</label>
                            <input type="text" class="form-control date-picker" id="toDate" 
                                placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
                        </div>

                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn w-100" style="background-color: #045E70;color:white;margin-top:31px">Search</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive" id="reportArea">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            
                            <th style="background-color: #045E70;color:white;">Invoice No</th>
                            <th style="background-color: #045E70;color:white;">Vendor Name</th>
                            <!-- <th style="background-color: #045E70;color:white;">Title</th> -->
                            <th style="background-color: #045E70;color:white;">Quantity</th>
                            <th style="background-color: #045E70;color:white;">Price</th>
                            <th style="background-color: #045E70;color:white;">Purchase Date</th>
                        </tr>
                    </thead>
                    <tbody id="booksTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Select date range to view purchased books
                            </td>
                        </tr>
                    </tbody>
                </table>
             </div>
        </div>
    </div>
</div>

<script>
const dateForm = document.getElementById('dateForm'),
      fromDate = document.getElementById('fromDate'),
      toDate = document.getElementById('toDate'),
      booksTableBody = document.getElementById('booksTableBody');

dateForm.addEventListener('submit', async e => {
    e.preventDefault();

    if (!fromDate.value || !toDate.value) {
        alert('Please select both dates');
        return;
    }

    try {
        const res = await fetch(`fetch_books_by_date.php?from=${fromDate.value}&to=${toDate.value}`);
        const html = await res.text();
        booksTableBody.innerHTML = html;
    } catch (err) {
        booksTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading books</td></tr>';
    }
});
</script>

<script>
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
