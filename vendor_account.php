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
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <!-- Search Form Card -->
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background-color: #045E70;">
                    <h5 class="card-title mb-0" style="color: white;">
                        <i class="fas fa-building me-2"></i>VENDOR ACCOUNT
                    </h5>
                </div>
                <div class="card-body" style="margin-top: 10px;">
                    <form id="vendorSearchForm" class="row g-3 align-items-center">
                        <div class="col-md-4 position-relative">
                            <label class="form-label fw-semibold">Vendor</label>
                            <input type="text" class="form-control" id="vendorInput" placeholder="Search Vendor" autocomplete="off">
                            <input type="hidden" id="vendor_id" name="vendor_id">
                            <div id="vendorResults" class="list-group position-absolute w-100 shadow" style="z-index:1000; display:none;"></div>
                        </div>

                    <div class="col-md-3">
    <label class="form-label fw-semibold">Start Date</label>
    <input type="text" class="form-control date-picker" id="startDate" name="startDate" 
           placeholder="dd/mm/yyyy" maxlength="10" required autocomplete="off">
    <input type="hidden" class="date-mysql" name="startDate_mysql">
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">End Date</label>
    <input type="text" class="form-control date-picker" id="endDate" name="endDate" 
           placeholder="dd/mm/yyyy" maxlength="10" required autocomplete="off">
    <input type="hidden" class="date-mysql" name="endDate_mysql">
</div>


                        <div class="col-md-2 d-flex align-items-end justify-content-end" style="margin-top: 40px;">
                            <button type="submit" class="btn w-100" style="background-color: #045E70;color:white;">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Search Results Heading -->
            <h5 id="searchResultsHeading" class="mt-4 d-none" style="color:#045E70; font-weight:600;">
                <i class="fas fa-list me-2"></i>Search Results
            </h5>

            <!-- Table Card -->
            <div class="card mt-2 d-none" id="resultsCard">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6; border-radius:0; font-size: 0.85rem;">
    <thead>
        <tr>
            <th style="background-color:#045E70; color:white; width:17%; font-size:11px;">Transaction</th>
            <th style="background-color:#045E70; color:white; width:17%; font-size:11px;">Date</th>
            <th style="background-color:#045E70; color:white; width:17%; font-size:11px;">Debit</th>
            <th style="background-color:#045E70; color:white; width:17%; font-size:11px;">Credit</th>
            <th style="background-color:#045E70; color:white; width:17%; font-size:11px;">Remaining Balance</th>
            <th style="background-color:#045E70; color:white; width:17%; font-size:11px;">Payment</th>
        </tr>
    </thead>
    <tbody id="resultsBody">
        <tr>
            <td colspan="6" class="text-center text-muted">No results yet</td>
        </tr>
    </tbody>
</table>

                    </div>
                </div>
            </div>
            
        </div>
    </div>
</div>

<script>
const input = document.getElementById('vendorInput'),
      results = document.getElementById('vendorResults'),
      vid = document.getElementById('vendor_id'),
      form = document.getElementById('vendorSearchForm'),
      resultsCard = document.getElementById('resultsCard'),
      searchHeading = document.getElementById('searchResultsHeading');

input.addEventListener('input', () => {
    vid.value = '';
    fetch('search_vendor.php?q=' + input.value)
        .then(r => r.text())
        .then(html => { 
            results.innerHTML = html; 
            results.style.display = html ? 'block' : 'none'; 
        });
});

results.addEventListener('click', e => { 
    let item = e.target.closest('.vendor-item'); 
    if(item){ 
        input.value = item.textContent; 
        vid.value = item.dataset.id; 
        results.style.display = 'none'; 
    }
});

form.addEventListener('submit', e => {
    e.preventDefault();
    let params = new URLSearchParams(); 
    vid.value ? params.append('vendor_id', vid.value) : params.append('vendor_name', input.value);
    let start = document.getElementById('startDate').value, 
        end = document.getElementById('endDate').value;
    if(start) params.append('start', start); 
    if(end) params.append('end', end);

    fetch('fetch_transactions.php?' + params)
        .then(r => r.text())
        .then(html => {
            resultsCard.classList.remove('d-none');
            searchHeading.classList.remove('d-none');
            document.getElementById('resultsBody').innerHTML = html || '<tr><td colspan="10" class="text-center text-muted">No results found</td></tr>';
        });
});
</script>


<?php include 'footer.php'; ?>
