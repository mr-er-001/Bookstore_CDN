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
                    BOOK WISE REPORT
                </h4>
            </div>

            <!-- Search Form -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-10">
                    <form id="bookReportForm" class="d-flex flex-wrap gap-3 align-items-end justify-content-center">

                        <!-- Book Search with Suggestions -->
                        <div class="d-flex flex-column position-relative" style="min-width: 220px;">
                            <label class="form-label fw-semibold mb-1">Book</label>
                            <input type="text" id="bookSearch" class="form-control book-search" placeholder="Type book title" autocomplete="off">
                            <input type="hidden" id="bookId" class="book-id">

                            <!-- Suggestions -->
                            <div id="bookSuggestions" 
                                class="list-group position-absolute shadow-sm" 
                                style="z-index:1000; top:100%; left:0; width:100%; font-size:13px; max-height:200px; overflow-y:auto; display:none;">
                            </div>
                        </div>

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
                            <th style="background-color: #045E70;color:white;">Invoice #</th>
                            <th style="background-color: #045E70;color:white;">Title</th>
                            <th style="background-color: #045E70;color:white;">Quantity</th>
                            <th style="background-color: #045E70;color:white;">Date</th>
                            <th style="background-color: #045E70;color:white;">Price</th>
                        </tr>
                    </thead>
                    <tbody id="bookReportTableBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-lg mb-2 d-block"></i>
                                Select book and date range to view report
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<style>
/* Small font + clean look for suggestions */
#bookSuggestions .list-group-item {
    font-size: 13px;
    padding: 6px 10px;
    cursor: pointer;
}

#bookSuggestions .list-group-item:hover {
    background-color: #f0f0f0;
}
</style>

<script>
// --- DOM Elements ---
const bookSearch = document.querySelector('.book-search'),
      bookIdInput = document.querySelector('.book-id'),
      fromDate = document.getElementById('fromDate'),
      toDate = document.getElementById('toDate'),
      bookSuggestions = document.getElementById('bookSuggestions'),
      bookReportForm = document.getElementById('bookReportForm'),
      bookReportTableBody = document.getElementById('bookReportTableBody');

// --- Autocomplete Book ---
bookSearch.addEventListener('keyup', () => {
    const query = bookSearch.value.trim();
    bookIdInput.value = "";
    bookSuggestions.innerHTML = "";
    bookSuggestions.style.display = "none"; // hide first
    if(!query) return;

    const xhr = new XMLHttpRequest();
    xhr.open('GET', 'fetch_book_suggestions_ajax.php?q=' + encodeURIComponent(query), true);
    xhr.onload = function() {
        if(this.status === 200){
            bookSuggestions.innerHTML = this.responseText;
            if (this.responseText.trim() !== "") {
                bookSuggestions.style.display = "block";
            }
            document.querySelectorAll('.suggestion-item').forEach(item => {
                item.addEventListener('click', function(){
                    bookSearch.value = this.dataset.name;
                    bookIdInput.value = this.dataset.id;
                    bookSuggestions.innerHTML = '';
                    bookSuggestions.style.display = "none";
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
        bookSuggestions.style.display = "none";
    }
});

// --- Fetch report ---
bookReportForm.addEventListener('submit', e => {
    e.preventDefault();
    if(!bookIdInput.value || !fromDate.value || !toDate.value){
        alert("Please select a book and date range");
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open('GET', `fetch_book_report.php?book=${bookIdInput.value}&from=${fromDate.value}&to=${toDate.value}`, true);
    xhr.onload = function(){
        if(this.status === 200){
            bookReportTableBody.innerHTML = this.responseText;
        }
    };
    xhr.send();
});
</script>
<?php include 'footer.php'; ?>
