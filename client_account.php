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

            <!-- Client Search Form Card -->
            <div class="card shadow-sm">
                <div class="card-header text-white" style="background-color: #045E70;">
                    <h5 class="card-title mb-0" style="color:white;">
                        <i class="fas fa-building me-2"></i>CLIENT ACCOUNT
                    </h5>
                </div>
                <div class="card-body" style="margin-top:10px;">
                    <form id="clientSearchForm" class="row g-3 align-items-center">
                        <div class="col-md-4 position-relative">
                            <label class="form-label fw-semibold">Client</label>
                            <input type="text" class="form-control" id="clientInput" placeholder="Search Client" autocomplete="off">
                            <input type="hidden" id="client_id" name="client_id">
                            <div id="clientResults" class="list-group position-absolute w-100 shadow" style="z-index:1000; display:none;"></div>
                        </div>
                       <div class="col-md-3">
    <label class="form-label fw-semibold">Start Date</label>
    <input type="text" class="form-control date-picker" id="startDate" name="startDate"
           placeholder="dd/mm/yyyy" maxlength="10" autocomplete="off" required>
    <input type="hidden" class="date-mysql" name="startDate_mysql">
</div>

<div class="col-md-3">
    <label class="form-label fw-semibold">End Date</label>
    <input type="text" class="form-control date-picker" id="endDate" name="endDate"
           placeholder="dd/mm/yyyy" maxlength="10" autocomplete="off" required>
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

            <!-- Results Table Card -->
            <div class="card mt-2 d-none" id="resultsCard">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle" style="table-layout: fixed; width:100%; border:1px solid #dee2e6; font-size:0.85rem;">
                            <thead>
                                <tr>
                                   
                                    <th style="background-color:#045E70;color:white;font-size:14px;width: 17%;">Client Name</th>
                                    <th style="background-color:#045E70;color:white;font-size:14px;width: 17%;">Date</th>
                                    <th style="background-color:#045E70;color:white;font-size:14px;width: 17%;">Debit</th>
                                    <th style="background-color:#045E70;color:white;font-size:14px;width: 17%;">Credit</th>
                                    <th style="background-color:#045E70;color:white;font-size:14px;width: 17%;">Remaining Balance</th>
                                    <th style="background-color:#045E70;color:white;font-size:14px;width: 17%;">Payment Mode</th>
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

<style>
    /* Small font for table headers and cells */
   #resultsBody td, #resultsBody th {
    font-size: 14px;
    padding: 8px 10px;
}


    /* Labels and inputs slightly smaller */
    .form-label, .form-control, .btn {
        font-size: 0.9rem;
    }

    /* Dropdown items */
    #clientResults .list-group-item {
        font-size: 0.85rem;
        padding: 6px 10px;
    }
</style>

<script>
const clientInput = document.getElementById('clientInput'),
      clientResults = document.getElementById('clientResults'),
      clientId = document.getElementById('client_id'),
      clientForm = document.getElementById('clientSearchForm'),
      resultsCard = document.getElementById('resultsCard'),
      searchHeading = document.getElementById('searchResultsHeading');

// Search client dropdown
clientInput.addEventListener('input', () => {
    clientId.value = '';
    let q = clientInput.value.trim();
    if(!q){ clientResults.style.display='none'; return; }

    fetch('search_client.php?q=' + encodeURIComponent(q))
        .then(res => res.text())
        .then(html => {
            clientResults.innerHTML = html;
            clientResults.style.display = html.trim() ? 'block' : 'none';
        });
});

// Click client from dropdown
clientResults.addEventListener('click', e => {
    let item = e.target.closest('.client-item');
    if(!item) return;
    clientInput.value = item.textContent;
    clientId.value = item.dataset.id;
    clientResults.style.display = 'none';
});

// Submit form to fetch transactions
clientForm.addEventListener('submit', e => {
    e.preventDefault();
    let params = new URLSearchParams();
    if(clientId.value) params.append('client_id', clientId.value);
    else params.append('client_name', clientInput.value.trim());
    let start = document.getElementById('startDate').value;
    let end = document.getElementById('endDate').value;
    if(start) params.append('start', start);
    if(end) params.append('end', end);

    fetch('fetch_client_transactions.php?' + params.toString())
        .then(res => res.text())
      .then(html => {
    const resultsBody = document.getElementById('resultsBody');
    resultsBody.innerHTML = html || '<tr><td colspan="10" class="text-center text-muted">No results found</td></tr>';
    resultsCard.classList.remove('d-none');
    searchHeading.classList.remove('d-none');
    resultsCard.scrollIntoView({behavior:'smooth'});

    // 🎨 Increase table font size dynamically
    resultsBody.style.fontSize = '14px';
});

});

// Hide dropdown when clicked outside
document.addEventListener('click', e => {
    if(!clientResults.contains(e.target) && e.target !== clientInput){
        clientResults.style.display = 'none';
    }
});
</script>

<?php include 'footer.php'; ?>
