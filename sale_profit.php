<?php include 'topheader.php'; ?>
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">

            <!-- Header -->
            <div class=" border rounded p-3 mb-4" style="background-color: #045E70;color:white;">
                <h4 class="text-center mb-0" style="background-color: #045E70;color:white; padding: 12px; font-weight: 600;">
                    SALE PROFIT REPORT
                </h4>
            </div>
<style>
            #bookSuggestions {
    position: absolute;
    top: 100%;         /* show below the input */
    left: 0;           /* align with input left */
    width: 100%;       /* same width as input */
    background: #fff;
    border: 1px solid #ddd;
    border-top: none;
    max-height: 200px; /* scroll if many items */
    overflow-y: auto;
    z-index: 1000;
    font-size: 13px;   /* smaller text */
}

#bookSuggestions .list-group-item {
    padding: 6px 10px;
    cursor: pointer;
}

#bookSuggestions .list-group-item:hover {
    background: #045E70;
    color: #fff;
}
</style>

            <!-- Search Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-10 position-relative">
                    <form id="bookProfitForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">

                       <div class="d-flex flex-column position-relative" style="min-width: 350px;"> 
    <label class="form-label fw-semibold mb-1">Book</label>
    <input type="text" id="bookSearch" class="form-control" placeholder="Type book name" autocomplete="off">
    <input type="hidden" id="bookId">
    <div id="bookSuggestions" class="list-group shadow-sm"></div>
</div>

                        <div class="d-flex flex-column" style="min-width: 180px;">
                            <label class="form-label fw-semibold mb-1">From</label>
                            <input type="text" class="form-control date-picker" id="fromDate" 
       placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off" style="max-width:150px;">
<input type="hidden" id="fromDate_mysql" name="fromDate_mysql">
                        </div>

                        <div class="d-flex flex-column" style="min-width: 180px;">
                            <label class="form-label fw-semibold mb-1">To</label>
                            <input type="text" class="form-control date-picker" id="toDate" 
       placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off" style="max-width:150px;">
<input type="hidden" id="toDate_mysql" name="toDate_mysql">
                        </div>

                        <div class="d-flex align-items-end">
                            <button type="submit" class="btn px-4 search-btn" style="background-color: #045E70;color:white;">Search</button>
                        </div>

                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive shadow-sm rounded">
                <table class="table table-bordered table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="background-color: #045E70;color:white;">ISBN</th>
                            <th style="background-color: #045E70;color:white;">Title</th>
                            <th style="background-color: #045E70;color:white;">Purchase Price</th>
                            <th style="background-color: #045E70;color:white;">Sale Price</th>
                            <th style="background-color: #045E70;color:white;">Quantity</th>
                            <th style="background-color: #045E70;color:white;">Discount</th>
                            <th style="background-color: #045E70;color:white;">Invoice No</th>
                            <th style="background-color: #045E70;color:white;">Date</th>
                            <th style="background-color: #045E70;color:white;">Price</th>
                            <th style="background-color: #045E70;color:white;">Profit</th>
                        </tr>
                    </thead>
                    <tbody id="profitTableBody">
                        <tr>
                            <td colspan="10" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-lg mb-2 d-block"></i>
                                Select book and date range to view profit
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<script>
// --- DOM Elements ---
const bookSearch = document.getElementById('bookSearch'),
      bookIdInput = document.getElementById('bookId'),
      bookSuggestions = document.getElementById('bookSuggestions'),
      bookProfitForm = document.getElementById('bookProfitForm'),
      profitTableBody = document.getElementById('profitTableBody');

// --- Autocomplete for books ---
bookSearch.addEventListener('keyup', () => {
    const query = bookSearch.value.trim();
    bookIdInput.value = "";
    bookSuggestions.innerHTML = "";
    if(!query) return;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'book_suggestions_ajax.php?q=' + encodeURIComponent(query), true);
    xhr.onload = function() {
        if(this.status === 200){
            bookSuggestions.innerHTML = this.responseText;
            document.querySelectorAll('.book-suggestion').forEach(item => {
                item.addEventListener('click', function(){
                    bookSearch.value = this.dataset.name;
                    bookIdInput.value = this.dataset.id;
                    bookSuggestions.innerHTML = '';
                });
            });
        }
    };
    xhr.send();
});

// Hide suggestions when click outside
document.addEventListener('click', e => {
    if(!bookSuggestions.contains(e.target) && e.target !== bookSearch){
        bookSuggestions.innerHTML = "";
    }
});

// --- Fetch Profit ---
bookProfitForm.addEventListener('submit', e => {
    e.preventDefault();
    const bookId = bookIdInput.value;
    const from = document.getElementById('fromDate').value;
    const to = document.getElementById('toDate').value;

    if(!bookId || !from || !to){
        alert("Please select a book and date range");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `fetch_sale_profit.php?book=${bookId}&from=${from}&to=${to}`, true);
    xhr.onload = function(){
        if(this.status === 200){
            profitTableBody.innerHTML = this.responseText;
        }
    };
    xhr.send();
});
</script>
<?php include 'footer.php'; ?>
