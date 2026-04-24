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
                    BOOKS BY CLASS
                </h4>
            </div>

            <!-- Search Section -->
            <div class="row justify-content-center mb-4">
                <div class="col-md-6">
                    <form id="bookClassForm" class="d-flex gap-2 align-items-center justify-content-center">
                        <label class="form-label mb-0 fw-semibold">Search Class:</label>
                        <div class="position-relative" style="width: 300px;">
                            <!-- Input -->
                            <input type="text" id="classInput" class="form-control" placeholder="Type class" autocomplete="off">
                            <!-- Suggestions dropdown -->
                            <div id="classSuggestions" class="list-group position-absolute w-100 shadow" style="z-index:1000; display:none;"></div>
                        </div>
                        <!-- <button type="submit" class="btn" style="background-color: #045E70;color:white;">Search</button> -->
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
                                Type a class to view books
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
const input = document.getElementById('classInput'),
      suggestions = document.getElementById('classSuggestions'),
      tableBody = document.getElementById('booksTableBody'),
      printBtn = document.getElementById('printButton'),
      printContainer = document.getElementById('printButtonContainer');

/* ------------------------------
   KEYBOARD DROPDOWN NAVIGATION
   FOR CLASS SEARCH INPUT
--------------------------------*/

let classIndex = -1; // Track highlighted item

document.addEventListener("keydown", function (e) {
    const items = suggestions.querySelectorAll(".list-group-item");

    if (!items.length || suggestions.style.display === "none") return;

    // ⬇️ Arrow Down
    if (e.key === "ArrowDown") {
        e.preventDefault();
        classIndex = (classIndex + 1) % items.length;
        highlightClassItem(items);
    }

    // ⬆️ Arrow Up
    else if (e.key === "ArrowUp") {
        e.preventDefault();
        classIndex = (classIndex - 1 + items.length) % items.length;
        highlightClassItem(items);
    }

    // ✔️ ENTER -> select class
    else if (e.key === "Enter") {
        if (classIndex >= 0) {
            e.preventDefault();
            items[classIndex].click();
            suggestions.style.display = "none";
            classIndex = -1;
        }
    }

    // ❌ ESC -> close dropdown
    else if (e.key === "Escape") {
        suggestions.style.display = "none";
        classIndex = -1;
    }
});

// Highlight function
function highlightClassItem(items) {
    items.forEach(i => i.classList.remove("active-item"));
    items[classIndex].classList.add("active-item");
}

// CSS for highlight
const style = document.createElement("style");
style.innerHTML = `
.active-item {
    background-color: #045E70 !important;
    color: white !important;
}
`;
document.head.appendChild(style);


// Show class suggestions (dropdown)
input.addEventListener('input', async () => {
    const q = input.value.trim();
    if (!q) {
        suggestions.style.display = 'none';
        return;
    }
    const res = await fetch(`fetch_classes.php?query=${encodeURIComponent(q)}`);
    const classes = await res.json();

    suggestions.innerHTML = '';
    if (classes.length > 0) {
        classes.forEach(c => {
            let option = document.createElement("button");
            option.type = "button";
            option.className = "list-group-item list-group-item-action";
            option.textContent = c.name;
            option.onclick = () => {
                input.value = c.name;
                fetchBooks(c.name);   // use your existing function
                suggestions.style.display = 'none';
            };
            suggestions.appendChild(option);
        });
        suggestions.style.display = "block";
    } else {
        suggestions.style.display = "none";
    }
});

// Fetch books
async function fetchBooks(cls) {
    const res = await fetch(`fetch_books_by_class.php?class=${encodeURIComponent(cls)}`);
    const books = await res.json();
    tableBody.innerHTML = books.length
        ? books.map(b => `<tr><td>${b.isbn}</td><td>${b.title}</td></tr>`).join('')
        : '<tr><td colspan="2" class="text-center text-muted">No books found</td></tr>';
    printContainer.style.display = books.length ? 'block' : 'none';
}

// Print table
printBtn.addEventListener('click', () => {
    const w = window.open('', '', 'width=800,height=600');
    w.document.write(`<html><head><title>Print Books</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"></head>
        <body><h3 class="text-center">Books - ${input.value}</h3>${document.querySelector('.table').outerHTML}</body></html>`);
    w.document.close();
    w.print();
});

// Hide dropdown when clicking outside
document.addEventListener("click", (e) => {
    if (!suggestions.contains(e.target) && e.target !== input) {
        suggestions.style.display = "none";
    }
});
</script>

<?php include 'footer.php'; ?>
