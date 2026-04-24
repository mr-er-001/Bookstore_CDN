
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
                <h4 class="text-center mb-0" style="background-color: #045E70;color:white; padding: 12px; font-weight: 600;">
                    SALE BOOKS BY CLIENT AND DATE
                </h4>
            </div>

            <!-- Search Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-10">
                    <form id="clientDateForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">

                        <!-- Client Search -->
                       <!-- Client Search -->
<div class="d-flex flex-column position-relative" style="min-width: 300px;">
    <label class="form-label fw-semibold mb-1">Client</label>
    <input type="text" class="form-control search-client" placeholder="Enter client name" autocomplete="off">
    <input type="hidden" class="client-id" name="client_id">
    <div class="list-group clientResults position-absolute w-100 shadow" 
         style="z-index:1000; display:none; top:100%; left:0;"></div>
</div>



                        <!-- Date Range -->
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
                            <th style="background-color: #045E70;color:white;">Client Name</th>
                            <th style="background-color: #045E70;color:white;">Invoice #</th>
                            <!-- <th style="background-color: #045E70;color:white;">Title</th> -->
                            <th style="background-color: #045E70;color:white;">Purchase Date</th>
                            <th style="background-color: #045E70;color:white;">Price</th>
                            <th style="background-color: #045E70;color:white;">Net Price</th>
                            
                            <th style="background-color: #045E70;color:white;">Action</th>
                        </tr>
                    </thead>
                    <tbody id="booksTableBody">
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                Select client and date range to view purchases
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
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<script>
$(document).ready(function() {

    // Search clients as you type
    $(document).on('keyup', '.search-client', function() {
        let input = $(this);
        let query = input.val();
        let resultsBox = input.siblings('.clientResults');

        if(query.length > 0){ 
            $.post('fetch_client.php', {search: query}, function(data){ 
                resultsBox.html(data).show(); 
            }); 
        } else {
            resultsBox.hide();
        }
    });

    // Click on a suggestion
    $(document).on('click', '.client-item', function(e){
        e.preventDefault();
        let item = $(this);
        $('.search-client').val(item.text());
        $('.client-id').val(item.data('id'));
        $('.clientResults').hide();
    });

    // Optional: hide results when clicking outside
    $(document).click(function(e) {
        if (!$(e.target).closest('.search-client, .clientResults').length) {
            $('.clientResults').hide();
        }
    });

});
</script>

<script>

    
const clientInput = document.querySelector('.search-client'),
      clientResults = document.querySelector('.clientResults'),
      clientId = document.querySelector('.client-id'),
      clientForm = document.getElementById('clientDateForm'),
      fromDate = document.getElementById('fromDate'),
      toDate = document.getElementById('toDate'),
      booksTableBody = document.getElementById('booksTableBody'),
      printButtonContainer = document.getElementById('printButtonContainer'),
      printButton = document.getElementById('printButton');

// Fetch purchases on submit
clientForm.addEventListener('submit', e => {
    e.preventDefault();

    if (!clientId.value || !fromDate.value || !toDate.value) {
        alert("Please select a client and both dates");
        return;
    }

    console.log(clientId.value, fromDate.value, toDate.value); // Debug

    fetch(`fetch_client_report.php?client=${clientId.value}&from=${fromDate.value}&to=${toDate.value}`)
        .then(r => r.text())
        .then(html => {
            booksTableBody.innerHTML = html;
            printButtonContainer.style.display = html.includes('No sales found') ? 'none' : 'block';
        })
        .catch(err => {
            console.error(err);
            booksTableBody.innerHTML = '<tr><td colspan="6" class="text-center text-danger">Error loading purchases</td></tr>';
            printButtonContainer.style.display = 'none';
        });
});

// Print table
printButton.addEventListener('click', () => {
    const table = document.querySelector('.table').outerHTML;
    const w = window.open('', '', 'width=900,height=700');
    w.document.write(`<html><head><title>Client Purchases</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        </head><body>
        <h3 class="text-center mb-3">Client Purchases - ${clientInput.value}</h3>${table}</body></html>`);
    w.document.close(); 
    w.print();
});

</script>

<?php include 'footer.php'; ?>
