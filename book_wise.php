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
                    CATEGORY BOOKS
                </h4>
            </div>

            <!-- Search Section -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-8">
                    <form id="bookCategoryForm" class="d-flex gap-2 align-items-center justify-content-center">
                        <label class="form-label mb-0 fw-semibold">Select category:</label>
           <select class="form-select" id="bookCategory" style="width: 200px;">
    <option value="">-- Select Category --</option>
    <?php
    include 'dbb.php';
    $catQuery = mysqli_query($conn, "SELECT id, category_name FROM category ORDER BY category_name ASC");
    if (mysqli_num_rows($catQuery) > 0) {
        while ($row = mysqli_fetch_assoc($catQuery)) {
            echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
        }
    } else {
        echo "<option disabled>No categories found</option>";
    }
    ?>
</select>

    <button type="submit" class="btn" style="background-color: #045E70;color:white;">Search</button>
</form>
                </div>
            </div>

            <!-- Results Table -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th style="background-color: #045E70;color:white;">ISBN</th>
                            <th style="background-color: #045E70;color:white;">Title</th>
                        </tr>
                    </thead>
                    <tbody id="booksTableBody">
                        <tr>
                            <td colspan="2" class="text-center text-muted py-4">
                                <i class="fas fa-search fa-2x mb-2 d-block"></i>
                                Select a language to view books
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Print Button -->
            <div class="text-end my-3" id="printButtonContainer" style="display:none;">
                <button class="btn btn-success" id="printButton">
                    <i class="fas fa-print"></i> Print
                </button>
            </div>

        </div>
    </div>
</div>

<script>
const bookCategory = document.getElementById('bookCategory'),
      bookForm = document.getElementById('bookCategoryForm'),
      booksTableBody = document.getElementById('booksTableBody'),
      printButtonContainer = document.getElementById('printButtonContainer'),
      printButton = document.getElementById('printButton');

// Fetch books by category
bookForm.addEventListener('submit', e => {
    e.preventDefault();
    if (!bookCategory.value) return alert('Please select a category');

    fetch(`fetch_books_by_language.php?category=${encodeURIComponent(bookCategory.value)}`)
        .then(r => r.text())
        .then(html => {
            booksTableBody.innerHTML = html;
            printButtonContainer.style.display = html.includes('No books found') ? 'none' : 'block';
        })
        .catch(() => {
            booksTableBody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error loading books</td></tr>';
            printButtonContainer.style.display = 'none';
        });
});

// Print table
printButton.addEventListener('click', () => {
    const tableHTML = document.querySelector('.table').outerHTML;
    const w = window.open('', '', 'width=800,height=600');
    w.document.write(`<html><head><title>Print Books</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        </head><body><h3 class="text-center">Books - ${bookCategory.value}</h3>${tableHTML}</body></html>`);
    w.document.close(); 
    w.print();
});
</script>


<?php include 'footer.php'; ?>
