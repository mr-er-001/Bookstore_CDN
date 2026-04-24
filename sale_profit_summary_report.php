<?php include 'topheader.php'; ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            <div class="border rounded p-3 mb-4" style="background-color: #045E70; color:white;">
                <h4 class="text-center mb-0" style="font-weight: 600;">SALE PROFIT SUMMARY REPORT</h4>
            </div>

            <style>
                .tbl-header  { background-color: #045E70; color: white; }
                .subtotal-row { background-color: #d0eaf0; font-weight: 600; }
                .grand-row   { background-color: #045E70; color: white; font-weight: bold; }
                .profit-pos  { color: #198754; }
                .profit-neg  { color: #dc3545; }
                .grand-row td { color: white !important; }
            </style>

            <!-- Search Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-8">
                    <form id="summaryForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">

                        <!-- Group By Toggle -->
                        <div class="d-flex flex-column" style="min-width: 180px;">
                            <label class="form-label fw-semibold mb-1">Group By</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="groupBy" id="groupDay" value="day" checked>
                                <label class="btn btn-outline-secondary" for="groupDay">Day by Day</label>
                                <input type="radio" class="btn-check" name="groupBy" id="groupMonth" value="month">
                                <label class="btn btn-outline-secondary" for="groupMonth">Month by Month</label>
                            </div>
                        </div>

                        <!-- From -->
                        <div class="d-flex flex-column" style="min-width: 150px;">
                            <label class="form-label fw-semibold mb-1">From</label>
                            <input type="text" class="form-control date-picker" id="fromDate"
                                placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
                        </div>

                        <!-- To -->
                        <div class="d-flex flex-column" style="min-width: 150px;">
                            <label class="form-label fw-semibold mb-1">To</label>
                            <input type="text" class="form-control date-picker" id="toDate"
                                placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
                        </div>

                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn px-4" style="background-color:#045E70; color:white;">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="tbl-header">Period</th>
                            <th class="tbl-header">Title</th>
                            <th class="tbl-header">ISBN</th>
                            <th class="tbl-header">Qty Sold</th>
                            <th class="tbl-header">Total Cost</th>
                            <th class="tbl-header">Total Revenue</th>
                            <th class="tbl-header">Profit</th>
                            <th class="tbl-header">Profit %</th>
                        </tr>
                    </thead>
                    <tbody id="summaryTableBody">
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="fas fa-chart-bar fa-lg mb-2 d-block"></i>
                                Select date range and click Search
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
document.getElementById('summaryForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const from    = document.getElementById('fromDate').value;
    const to      = document.getElementById('toDate').value;
    const groupBy = document.querySelector('input[name="groupBy"]:checked').value;

    if (!from || !to) { alert("Please select a date range"); return; }

    const tbody = document.getElementById('summaryTableBody');
    tbody.innerHTML = `<tr><td colspan="8" class="text-center text-muted py-3">
        <i class="fas fa-spinner fa-spin me-2"></i>Loading...</td></tr>`;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `fetch_profit_summary.php?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&group=${groupBy}`, true);
    xhr.onload = function() {
        if (this.status === 200) tbody.innerHTML = this.responseText;
    };
    xhr.send();
});
</script>

<?php include 'footer.php'; ?>