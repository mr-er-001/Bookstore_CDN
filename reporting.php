<?php include 'topheader.php'; ?>
<div class="container py-5">
    <h2 class="text-center fw-bold mb-5">
        <i class="bi bi-bar-chart-line-fill me-2 text-primary"></i> Reporting Dashboard
    </h2>

    <?php
    $reports = [
        ["file" => "pub_book.php", "title" => "Publisher Book", "icon" => "bi-book-half", "color" => "primary"],
        ["file" => "book_wise.php", "title" => "Category Book", "icon" => "bi-journal-bookmark-fill", "color" => "primary"],
        ["file" => "class_wise.php", "title" => "Class Book", "icon" => "bi-mortarboard-fill", "color" => "primary"],
        ["file" => "purchase_report.php", "title" => "Purchase Report", "icon" => "bi-cart-check-fill", "color" => "primary"],
        ["file" => "sale_report.php", "title" => "Sale Report", "icon" => "bi-receipt-cutoff", "color" => "primary"],
        ["file" => "sales_return.php", "title" => "Sale Return", "icon" => "bi-arrow-counterclockwise", "color" => "primary"],
        ["file" => "client_report.php", "title" => "Client Report", "icon" => "bi-person-lines-fill", "color" => "primary"],
        ["file" => "client_invoice_report.php", "title" => "Client Report by Invoice", "icon" => "bi-file-earmark-text-fill", "color" => "primary"],
        ["file" => "vendor_report.php", "title" => "Vendor Report", "icon" => "bi-building", "color" => "primary"],
        ["file" => "book_wise_report.php", "title" => "Book Wise", "icon" => "bi-journals", "color" => "primary"],
        ["file" => "invoice_date_wise.php", "title" => "Invoice Wise", "icon" => "bi-calendar-check-fill", "color" => "primary"],
        ["file" => "sale_profit.php", "title" => "Sale/Profit", "icon" => "bi-graph-up-arrow", "color" => "primary"],
        ["file" => "sale_profit_summary_report.php", "title" => "Sale/Profit Summary", "icon" => "bi-graph-up-arrow", "color" => "primary"],
        ["file" => "sale_profit_publisher_report.php", "title" => "Sale/Profit by Publisher", "icon" => "bi-graph-up-arrow", "color" => "primary"]
    ];
    ?>

    <div class="row g-4">
        <?php foreach ($reports as $report): ?>
            <div class="col-12 col-sm-6 col-lg-4">
                <div class="card border-0 shadow-lg h-100 rounded-4 hover-card">
                    <div class="card-body text-center py-5 position-relative">
                        <!-- Removed per-card gradient class -->
                        <div class="icon-circle text-white mb-4 mx-auto shadow-lg">
                            <i class="bi <?= $report['icon'] ?> fs-2"></i>
                        </div>
                        <h5 class="fw-bold mb-3"><?= $report['title'] ?></h5>
                        <a href="<?= $report['file'] ?>" class="btn btn-outline-<?= $report['color'] ?> px-4">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Open Report
                        </a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
/* Page gradient background */
body {
    background: linear-gradient(160deg, #f8f9fa 0%, #e9ecef 100%);
}

/* Card hover */
.hover-card {
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    overflow: hidden;
}
.hover-card:hover {
    transform: translateY(-12px);
    box-shadow: 0 25px 50px rgba(0,0,0,0.2);
}

/* Icon animation */
.icon-circle {
    width: 80px;
    height: 80px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    /* Unified gradient for all icons */
    background: linear-gradient(135deg, #045E70, #0890A6) !important;
    color: #fff;
    font-size: 2rem;
    margin: 0 auto 15px auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.15);
    transition: transform 0.4s ease;
}

/* Hover effect for icon */
.hover-card:hover .icon-circle {
    transform: scale(1.2) rotate(10deg);
}

/* Fade-up animation on load */
@keyframes fadeUp {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
.hover-card { animation: fadeUp 0.6s ease forwards; }

/* Responsive tweaks */
@media (max-width: 575px){
    .card-body { padding: 2rem 1rem; }
    .icon-circle { width: 60px; height: 60px; }
}
</style>

<?php include 'footer.php'; ?>
