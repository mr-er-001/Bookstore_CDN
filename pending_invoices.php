<?php include 'topheader.php'; ?>

<?php
// Delete draft if requested
if (isset($_GET['delete_draft']) && isset($_GET['type'])) {
    $del_invoice = mysqli_real_escape_string($conn, $_GET['delete_draft']);
    $del_type = $_GET['type'];
    if ($del_type === 'sale') {
        mysqli_query($conn, "DELETE FROM sale_invoice WHERE invoice_no = '$del_invoice' AND status_invoice = 0");
    } elseif ($del_type === 'purchase') {
        mysqli_query($conn, "DELETE FROM purchase_invoice WHERE invoice_no = '$del_invoice' AND status_invoice = 0");
    }
    echo "<script>window.open('./pending_invoices.php','_self');</script>";
    exit;
}

// Fetch draft sale invoices (grouped by invoice_no)
$draft_sales = mysqli_query($conn, "
    SELECT si.invoice_no, si.invoice_date, c.company_name AS client_name, c.id AS client_id,
           COUNT(*) AS items, SUM(si.net_price) AS total
    FROM sale_invoice si
    LEFT JOIN client c ON si.client_id = c.id
    WHERE si.status_invoice = 0
    GROUP BY si.invoice_no
    ORDER BY si.invoice_no DESC
");

// Fetch draft purchase invoices (grouped by invoice_no)
$draft_purchases = mysqli_query($conn, "
    SELECT pi.invoice_no, pi.invoice_date, v.company_name AS vendor_name, v.id AS vendor_id,
           COUNT(*) AS items, SUM(pi.net_price) AS total
    FROM purchase_invoice pi
    LEFT JOIN vendor v ON pi.vendor_id = v.id
    WHERE pi.status_invoice = 0
    GROUP BY pi.invoice_no
    ORDER BY pi.invoice_no DESC
");
?>

<style>
body { background: #e5f4f9; font-family: 'Open Sans', sans-serif; }
.page-title { font-size: 1.5rem; font-weight: bold; color: #045E70; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
.card-premium { border-radius: 15px; border: none; box-shadow: 0 6px 20px rgba(0,0,0,0.08); background: #fff; }
.draft-badge { background: #f0ad4e; color: #fff; padding: 3px 10px; border-radius: 8px; font-size: 0.75rem; font-weight: 600; }
.table th { background: #045E70; color: #fff; font-size: 0.85rem; text-align: center; }
.table td { font-size: 0.85rem; text-align: center; vertical-align: middle; }
.btn-edit { background: #045E70; color: #fff; border-radius: 8px; padding: 4px 14px; font-size: 0.8rem; }
.btn-edit:hover { background: #03495a; color: #fff; }
.btn-delete { background: #dc3545; color: #fff; border-radius: 8px; padding: 4px 14px; font-size: 0.8rem; }
.btn-delete:hover { background: #b02a37; color: #fff; }
.tab-btn { padding: 10px 30px; font-weight: 600; font-size: 0.95rem; border: 2px solid #045E70; background: #fff; color: #045E70; cursor: pointer; transition: 0.3s; }
.tab-btn:first-child { border-radius: 10px 0 0 10px; }
.tab-btn:last-child { border-radius: 0 10px 10px 0; }
.tab-btn.active { background: #045E70; color: #fff; }
.tab-btn:hover:not(.active) { background: #e0f2f7; }
.draft-section { display: none; }
.draft-section.active { display: block; }
</style>

<?php
$sale_count = mysqli_num_rows($draft_sales);
$purchase_count = mysqli_num_rows($draft_purchases);
?>

<div class="container py-4">
    <div class="page-title">
        <i class="bi bi-hourglass-split"></i> Pending Draft Invoices
    </div>

    <!-- Toggle Buttons -->
    <div class="text-center mb-4">
        <button class="tab-btn active" id="tabSale" onclick="showTab('sale')">
            Sale Drafts
            <?php if ($sale_count > 0): ?>
                <span class="draft-badge ms-1"><?= $sale_count ?></span>
            <?php endif; ?>
        </button>
        <button class="tab-btn" id="tabPurchase" onclick="showTab('purchase')">
            Purchase Drafts
            <?php if ($purchase_count > 0): ?>
                <span class="draft-badge ms-1"><?= $purchase_count ?></span>
            <?php endif; ?>
        </button>
    </div>

    <!-- Draft Sale Invoices -->
    <div class="draft-section" id="sectionSale" style="display:block;">
        <div class="card card-premium mb-4">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Client</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($sale_count > 0): ?>
                            <?php $sn = 1; while ($row = mysqli_fetch_assoc($draft_sales)): ?>
                            <tr>
                                <td><?= $sn++ ?></td>
                                <td><?= $row['invoice_no'] ?></td>
                                <td><?= date('d-m-Y', strtotime($row['invoice_date'])) ?></td>
                                <td>
                                    <a href="sale_book.php?draft_id=<?= $row['invoice_no'] ?>" style="color:#045E70; font-weight:600; text-decoration:none;">
                                        <?= htmlspecialchars($row['client_name']) ?>
                                    </a>
                                </td>
                                <td><?= $row['items'] ?></td>
                                <td style="text-align:right;"><?= number_format($row['total'], 2) ?></td>
                                <td>
                                    <a href="sale_book.php?draft_id=<?= $row['invoice_no'] ?>" class="btn btn-edit btn-sm">Edit</a>
                                    <a href="pending_invoices.php?delete_draft=<?= $row['invoice_no'] ?>&type=sale" class="btn btn-delete btn-sm" onclick="return confirm('Delete this draft?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-muted py-3">No draft sale invoices</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Draft Purchase Invoices -->
    <div class="draft-section" id="sectionPurchase" style="display:none;">
        <div class="card card-premium mb-4">
            <div class="card-body p-0">
                <table class="table table-bordered table-striped mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Invoice No</th>
                            <th>Date</th>
                            <th>Vendor</th>
                            <th>Items</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($purchase_count > 0): ?>
                            <?php $sn = 1; while ($row = mysqli_fetch_assoc($draft_purchases)): ?>
                            <tr>
                                <td><?= $sn++ ?></td>
                                <td><?= $row['invoice_no'] ?></td>
                                <td><?= date('d-m-Y', strtotime($row['invoice_date'])) ?></td>
                                <td>
                                    <a href="book.php?draft_id=<?= $row['invoice_no'] ?>" style="color:#045E70; font-weight:600; text-decoration:none;">
                                        <?= htmlspecialchars($row['vendor_name']) ?>
                                    </a>
                                </td>
                                <td><?= $row['items'] ?></td>
                                <td style="text-align:right;"><?= number_format($row['total'], 2) ?></td>
                                <td>
                                    <a href="book.php?draft_id=<?= $row['invoice_no'] ?>" class="btn btn-edit btn-sm">Edit</a>
                                    <a href="pending_invoices.php?delete_draft=<?= $row['invoice_no'] ?>&type=purchase" class="btn btn-delete btn-sm" onclick="return confirm('Delete this draft?')">Delete</a>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="7" class="text-muted py-3">No draft purchase invoices</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
function showTab(type) {
    document.getElementById('tabSale').classList.remove('active');
    document.getElementById('tabPurchase').classList.remove('active');

    if (type === 'sale') {
        document.getElementById('sectionSale').style.display = 'block';
        document.getElementById('sectionPurchase').style.display = 'none';
        document.getElementById('tabSale').classList.add('active');
    } else {
        document.getElementById('sectionSale').style.display = 'none';
        document.getElementById('sectionPurchase').style.display = 'block';
        document.getElementById('tabPurchase').classList.add('active');
    }
}
</script>

<?php include 'footer.php'; ?>
