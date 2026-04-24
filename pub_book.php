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
            <!-- Header Section -->
            <div class=" border p-3 mb-3" style="background-color: #045E70;">
                <h4 class="text-center mb-0" style="background-color: #045E70; padding: 10px; font-weight: bold; color: white;">
                    PUBLISHER BOOKS
                </h4>
            </div>

            <!-- Search Section -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form id="publisherBooksForm" class="d-flex gap-2 align-items-center justify-content-center">
                        <label class="form-label mb-0 fw-semibold">Search Books of Publisher:</label>
                        <div class="position-relative" style="width: 300px;">
                            <input type="text" class="form-control" id="publisherInput" placeholder="Enter publisher name" autocomplete="off">
                            <input type="hidden" id="publisher_id" name="publisher_id">
                            <div id="publisherResults" class="list-group position-absolute w-100 shadow" style="z-index:1000; display:none;"></div>
                        </div>
                        <button type="submit" class="btn" style="background-color: #045E70;color:white;">search</button>
                    </form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                  <thead class="table-dark">
    <tr>
        <th style="background-color: #045E70; color: white; text-align: center;">Title</th>
        <th style="background-color: #045E70; color: white; text-align: center;">Sale Price</th>
        <th style="background-color: #045E70; color: white; text-align: center;">Purchase Price</th>
        <th style="background-color: #045E70; color: white; text-align: center;">Quantity</th>
        <th style="background-color: #045E70; color: white; text-align: center;">Total Price</th>
    </tr>
</thead>

                    <tbody id="booksTableBody">
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-2x mb-2 d-block"></i>
                                Search for a publisher to view their books
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Print Button (Hidden initially) -->
            <div class="text-end my-3" id="printButtonContainer" style="display:none;">
                <button class="btn btn-success" id="printButton">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>

        </div>
    </div>
</div>

<script>
const publisherInput = document.getElementById('publisherInput'),
      publisherResults = document.getElementById('publisherResults'),
      publisherId = document.getElementById('publisher_id'),
      publisherForm = document.getElementById('publisherBooksForm'),
      printButtonContainer = document.getElementById('printButtonContainer'),
      printButton = document.getElementById('printButton');

let selectedIndex = -1; // Track current highlighted index

// Fetch publishers as you type
publisherInput.addEventListener('input', () => {
    publisherId.value = '';
    const q = publisherInput.value.trim();
    if (!q) return publisherResults.style.display = 'none';
    fetch(`search_publisher.php?q=${encodeURIComponent(q)}`)
        .then(r => r.text())
        .then(html => {
            publisherResults.innerHTML = html;
            selectedIndex = -1; // reset selection
        })
        .finally(() => publisherResults.style.display = publisherResults.innerHTML ? 'block' : 'none');
});

// Keyboard navigation
publisherInput.addEventListener('keydown', e => {
    const items = Array.from(publisherResults.querySelectorAll('.publisher-item'));
    if (!items.length) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex = (selectedIndex + 1) % items.length;
        highlightItem(items);
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex = (selectedIndex - 1 + items.length) % items.length;
        highlightItem(items);
    } else if (e.key === 'Enter') {
        e.preventDefault();
        if (selectedIndex >= 0 && selectedIndex < items.length) {
            selectPublisher(items[selectedIndex]);
        } else {
            publisherForm.dispatchEvent(new Event('submit'));
        }
    } else if (e.key === 'Escape') {
        publisherResults.style.display = 'none';
    }
});

function highlightItem(items) {
    items.forEach((item, i) => {
        item.classList.toggle('active', i === selectedIndex);
    });
    if (selectedIndex >= 0) items[selectedIndex].scrollIntoView({block: 'nearest'});
}

// Select publisher function
function selectPublisher(item) {
    publisherInput.value = item.textContent;
    publisherId.value = item.dataset.id;
    publisherResults.style.display = 'none';
    publisherForm.dispatchEvent(new Event('submit'));
}

// Click selection
publisherResults.addEventListener('click', e => {
    const item = e.target.closest('.publisher-item');
    if (!item) return;
    selectPublisher(item);
});

// Hide dropdown on outside click
document.addEventListener('click', e => {
    if (!publisherInput.contains(e.target) && !publisherResults.contains(e.target))
        publisherResults.style.display = 'none';
});

// Fetch books
publisherForm.addEventListener('submit', e => {
    e.preventDefault();
    const params = new URLSearchParams(publisherId.value ? {publisher_id: publisherId.value} : {publisher_name: publisherInput.value});
    fetch(`fetch_publisher_books.php?${params}`)
        .then(r => r.text())
        .then(html => {
            document.getElementById('booksTableBody').innerHTML = html;
            printButtonContainer.style.display = html.includes('No books found') ? 'none' : 'block';
        })
        .catch(() => {
            document.getElementById('booksTableBody').innerHTML = '<tr><td colspan="5" class="text-center text-danger">Error loading books</td></tr>';
            printButtonContainer.style.display = 'none';
        });
});

// Print table
printButton.addEventListener('click', () => {
    const table = document.querySelector('.table').outerHTML;
    const w = window.open('', '', 'width=800,height=600');
    w.document.write(`<html><head><title>Print Books</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        </head><body><h3 class="text-center">Publisher Books</h3>${table}</body></html>`);
    w.document.close(); w.print();
});

// Direct search function
const searchPublisherBooks = name => { if(name) publisherInput.value = name; publisherForm.dispatchEvent(new Event('submit')); };

</script>

<style>
.publisher-item.active {
    background-color: #045E70;
    color: white;
}
</style>

<?php include 'footer.php'; ?>
