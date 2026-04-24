<?php include 'topheader.php'; ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            <div class="border rounded p-3 mb-4" style="background-color: #045E70; color:white;">
                <h4 class="text-center mb-0" style="font-weight: 600;">SALE PROFIT REPORT BY PUBLISHER</h4>
            </div>

            <style>
                .tbl-header      { background-color: #045E70; color: white; }
                .publisher-row   { background-color: #045E70; color: white; font-weight: 700; font-size: 0.95rem; }
                .period-row      { background-color: #e0f0f5; color: #045E70; font-weight: 600; border-top: 2px solid #045E70; }
                .subtotal-period { background-color: #cce8f0; font-weight: 600; }
                .subtotal-pub    { background-color: #9dd0de; font-weight: 700; }
                .grand-row       { background-color: #045E70; color: white; font-weight: bold; }
                .grand-row td    { color: white !important; }
                .profit-pos      { color: #198754; }
                .profit-neg      { color: #dc3545; }
                .publisher-row td { color: white !important; }

                #publisherSuggestions {
                    position: absolute; top: 100%; left: 0; width: 100%;
                    background: #fff; border: 1px solid #ddd; border-top: none;
                    max-height: 200px; overflow-y: auto; z-index: 1000; font-size: 13px;
                }
                #publisherSuggestions .list-group-item { padding: 6px 10px; cursor: pointer; }
                #publisherSuggestions .list-group-item:hover { background: #045E70; color: #fff; }
            </style>

            <!-- Search Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-10">
                    <form id="publisherProfitForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">

                        <!-- Publisher Search -->
                        <div class="d-flex flex-column position-relative" style="min-width: 280px;">
                            <label class="form-label fw-semibold mb-1">Publisher</label>
                            <input type="text" id="publisherSearch" class="form-control"
                                   placeholder="All publishers (or type to filter)" autocomplete="off">
                            <div id="publisherSuggestions" class="list-group shadow-sm"></div>
                        </div>

                        <!-- Group By Toggle -->
                        <div class="d-flex flex-column">
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
                            <th class="tbl-header">Publisher</th>
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
                    <tbody id="publisherTableBody">
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
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
// ── Publisher Autocomplete ────────────────────────────────────────────────────
const publisherSearch      = document.getElementById('publisherSearch');
const publisherSuggestions = document.getElementById('publisherSuggestions');
let activeSuggestionIndex  = -1;
// Track the currently confirmed publisher name (set when user selects from dropdown)
let confirmedPublisher     = '';

publisherSearch.addEventListener('input', () => {
    const q = publisherSearch.value.trim();

    // If the user changes the text after selecting, clear the confirmed value
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

// ── Mouse click selection ─────────────────────────────────────────────────────
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

// ── Keyboard navigation ───────────────────────────────────────────────────────
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

// ── Close on outside click ────────────────────────────────────────────────────
document.addEventListener('click', e => {
    if (!publisherSuggestions.contains(e.target) && e.target !== publisherSearch) {
        publisherSuggestions.innerHTML = '';
        activeSuggestionIndex = -1;
    }
});

// ── Form Submit ───────────────────────────────────────────────────────────────
document.getElementById('publisherProfitForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const from    = document.getElementById('fromDate').value.trim();
    const to      = document.getElementById('toDate').value.trim();
    const groupBy = document.querySelector('input[name="groupBy"]:checked').value;

    // Use confirmedPublisher if set, otherwise fall back to whatever is typed in the box
    const publisher = confirmedPublisher || publisherSearch.value.trim();

    if (!from || !to) {
        alert("Please select a date range");
        return;
    }

    const tbody = document.getElementById('publisherTableBody');
    tbody.innerHTML = `<tr><td colspan="9" class="text-center text-muted py-3">
        <i class="fas fa-spinner fa-spin me-2"></i>Loading...</td></tr>`;

    const url = `fetch_profit_publisher.php?from=${encodeURIComponent(from)}&to=${encodeURIComponent(to)}&group=${encodeURIComponent(groupBy)}&publisher=${encodeURIComponent(publisher)}`;

    fetch(url)
        .then(response => {
            if (!response.ok) throw new Error('Network response was not ok: ' + response.status);
            return response.text();
        })
        .then(html => {
            tbody.innerHTML = html;
        })
        .catch(err => {
            tbody.innerHTML = `<tr><td colspan="9" class="text-center text-danger py-3">
                <i class="fas fa-exclamation-triangle me-2"></i>Error loading data: ${err.message}</td></tr>`;
        });
});
</script>

<?php include 'footer.php'; ?>