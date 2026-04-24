<?php include 'topheader.php'; ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="border rounded p-3 mb-4" style="background-color: #045E70; color:white;">
                <h4 class="text-center mb-0" style="font-weight: 600;">PUBLISHER PURCHASE & STOCK REPORT</h4>
            </div>

            <style>
                .tbl-header      { background-color: #045E70; color: white; }
                .period-row      { background-color: #e0f0f5; color: #045E70; font-weight: 600; border-top: 2px solid #045E70; }
                .summary-row     { background-color: #f7fafc; font-weight: 700; }
                .publisher-row   { background-color: #045E70; color: white; font-weight: 700; }
                .stock-note      { background: #f1f7fb; border: 1px solid #d9edf7; padding: 16px; border-radius: 12px; }
                #publisherSuggestions { position: absolute; top: 100%; left: 0; width: 100%; background: #fff; border: 1px solid #ddd; border-top: none; max-height: 200px; overflow-y: auto; z-index: 1000; font-size: 13px; }
                #publisherSuggestions .list-group-item { padding: 6px 10px; cursor: pointer; }
                #publisherSuggestions .list-group-item:hover { background: #045E70; color: #fff; }
            </style>

            <div class="row justify-content-center mb-4">
                <div class="col-lg-10">
                    <form id="publisherPurchaseForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">
                        <div class="d-flex flex-column position-relative" style="min-width: 280px;">
                            <label class="form-label fw-semibold mb-1">Publisher</label>
                            <input type="text" id="publisherSearch" class="form-control" placeholder="All publishers (or type to filter)" autocomplete="off">
                            <div id="publisherSuggestions" class="list-group shadow-sm"></div>
                        </div>

                        <div class="d-flex flex-column">
                            <label class="form-label fw-semibold mb-1">Group By</label>
                            <div class="btn-group" role="group">
                                <input type="radio" class="btn-check" name="groupBy" id="groupDay" value="day" checked>
                                <label class="btn btn-outline-secondary" for="groupDay">Day by Day</label>
                                <input type="radio" class="btn-check" name="groupBy" id="groupMonth" value="month">
                                <label class="btn btn-outline-secondary" for="groupMonth">Month by Month</label>
                            </div>
                        </div>

                        <div class="d-flex flex-column" style="min-width: 150px;">
                            <label class="form-label fw-semibold mb-1">From</label>
                            <input type="text" class="form-control date-picker" id="fromDate" placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
                        </div>

                        <div class="d-flex flex-column" style="min-width: 150px;">
                            <label class="form-label fw-semibold mb-1">To</label>
                            <input type="text" class="form-control date-picker" id="toDate" placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
                        </div>

                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn px-4" style="background-color:#045E70; color:white;">
                                <i class="fas fa-search me-1"></i> Search
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <div class="row mb-3" id="reportSummary" style="display:none;">
                <div class="col-md-4 mb-3">
                    <div class="stock-note">
                        <strong>Current Stock Left:</strong>
                        <span id="stockLeft">0</span> books
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stock-note">
                        <strong>Current Stock Value:</strong>
                        <span id="stockValue">0.00</span> PKR
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <div class="stock-note">
                        <strong>Purchase Total in Range:</strong>
                        <span id="purchaseTotal">0.00</span> PKR
                    </div>
                </div>
            </div>

            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead>
                        <tr>
                            <th class="tbl-header">Publisher</th>
                            <th class="tbl-header">Period</th>
                            <th class="tbl-header">Qty Purchased</th>
                            <th class="tbl-header">Total Purchase Price</th>
                        </tr>
                    </thead>
                    <tbody id="purchaseTableBody">
                        <tr>
                            <td colspan="4" class="text-center text-muted py-4">
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
const publisherSearch      = document.getElementById('publisherSearch');
const publisherSuggestions = document.getElementById('publisherSuggestions');
let activeSuggestionIndex  = -1;
let confirmedPublisher     = '';

publisherSearch.addEventListener('input', () => {
    const q = publisherSearch.value.trim();
    confirmedPublisher = '';
    publisherSuggestions.innerHTML = '';
    activeSuggestionIndex = -1;
    if (!q) return;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'search_pub.php?q=' + encodeURIComponent(q), true);
    xhr.onload = function () {
        if (this.status === 200) {
            publisherSuggestions.innerHTML = this.responseText;
        }
    };
    xhr.send();
});

publisherSuggestions.addEventListener('mousedown', function(e) {
    const item = e.target.closest('.publisher-suggestion');
    if (item) {
        e.preventDefault();
        publisherSearch.value = item.dataset.name;
        confirmedPublisher    = item.dataset.name;
        publisherSuggestions.innerHTML = '';
        activeSuggestionIndex = -1;
    }
});

publisherSearch.addEventListener('keydown', function(e) {
    const items = publisherSuggestions.querySelectorAll('.publisher-suggestion');
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        activeSuggestionIndex = Math.min(activeSuggestionIndex + 1, items.length - 1);
        highlightSuggestion(items);

    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        activeSuggestionIndex = Math.max(activeSuggestionIndex - 1, 0);
        highlightSuggestion(items);

    } else if (e.key === 'Enter' || e.key === 'Tab') {
        if (activeSuggestionIndex >= 0 && items[activeSuggestionIndex]) {
            e.preventDefault();
            publisherSearch.value = items[activeSuggestionIndex].dataset.name;
            confirmedPublisher    = items[activeSuggestionIndex].dataset.name;
            publisherSuggestions.innerHTML = '';
            activeSuggestionIndex = -1;
        }

    } else if (e.key === 'Escape') {
        publisherSuggestions.innerHTML = '';
        activeSuggestionIndex = -1;
    }
});

function highlightSuggestion(items) {
    items.forEach((item, i) => {
        item.style.backgroundColor = i === activeSuggestionIndex ? '#045E70' : '';
        item.style.color           = i === activeSuggestionIndex ? '#fff'    : '';
    });
    if (items[activeSuggestionIndex]) {
        items[activeSuggestionIndex].scrollIntoView({ block: 'nearest' });
    }
}

document.addEventListener('click', e => {
    if (!publisherSuggestions.contains(e.target) && e.target !== publisherSearch) {
        publisherSuggestions.innerHTML = '';
        activeSuggestionIndex = -1;
    }
});

const form = document.getElementById('publisherPurchaseForm');
const tableBody = document.getElementById('purchaseTableBody');
const summaryRow = document.getElementById('reportSummary');
const stockLeft = document.getElementById('stockLeft');
const stockValue = document.getElementById('stockValue');
const purchaseTotal = document.getElementById('purchaseTotal');

form.addEventListener('submit', function(e) {
    e.preventDefault();

    const from = document.getElementById('fromDate').value.trim();
    const to   = document.getElementById('toDate').value.trim();
    const group = document.querySelector('input[name="groupBy"]:checked').value;
    const publisher = confirmedPublisher || publisherSearch.value.trim();

    if (!from || !to) {
        alert('Please select both dates');
        return;
    }

    tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i>Loading...</td></tr>`;

    fetch(`fetch_purchase_stock_publisher.php?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&group=${encodeURIComponent(group)}&publisher=${encodeURIComponent(publisher)}`)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
            return response.text();
        })
        .then(html => {
            if (html.trim() === '') {
                tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-muted py-3">No data found.</td></tr>`;
                summaryRow.style.display = 'none';
            } else {
                tableBody.innerHTML = html;
                summaryRow.style.display = 'flex';
                const stockLeftMatch = html.match(/data-stock-left="(\d+)"/);
                const stockValueMatch = html.match(/data-stock-value="([\d\.]+)"/);
                const purchaseMatch   = html.match(/data-purchase-total="([\d\.]+)"/);
                stockLeft.textContent  = stockLeftMatch ? stockLeftMatch[1] : '0';
                stockValue.textContent = stockValueMatch ? parseFloat(stockValueMatch[1]).toFixed(2) : '0.00';
                purchaseTotal.textContent = purchaseMatch ? parseFloat(purchaseMatch[1]).toFixed(2) : '0.00';
            }
        })
        .catch(err => {
            tableBody.innerHTML = `<tr><td colspan="4" class="text-center text-danger py-3"><i class="fas fa-exclamation-triangle me-2"></i>Error loading data: ${err.message}</td></tr>`;
            summaryRow.style.display = 'none';
        });
});
</script>

<?php include 'footer.php'; ?>
