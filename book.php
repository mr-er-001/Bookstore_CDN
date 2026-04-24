<?php
include 'topheader.php';
if (isset($_POST['save']) || isset($_POST['draft'])) {
    $is_draft = isset($_POST['draft']) ? 1 : 0;
    $status_invoice = $is_draft ? 0 : 1; // 0 = draft, 1 = saved

    $invoice_no = $_POST['invoice_no'];
    $pub_date = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['pub_date'])));
    $vendor_id = $_POST['vendor_id'];
    $books = $_POST['book_id'];
    $prices = $_POST['price'];
    $quantities = $_POST['quantity'];
    $discounts = $_POST['discount'];
    $totals = $_POST['total_price'];
    $net_prices = $_POST['net_price'];
    $grand_total = $_POST['grand_total'];
    $edit_draft_invoice = $_POST['edit_draft_invoice'] ?? null;

    // If editing an existing draft, delete old draft rows first
    if (!empty($edit_draft_invoice)) {
        mysqli_query($conn, "DELETE FROM purchase_invoice WHERE invoice_no = '$edit_draft_invoice' AND status_invoice = 0");
        $next_invoice = $edit_draft_invoice;
    } else {
        // Only check duplicate for non-draft saves
        if (!$is_draft) {
            $check = mysqli_query($conn, "SELECT id FROM purchase_invoice WHERE invoice_no = '$invoice_no' AND status_invoice = 1 LIMIT 1");
            if (mysqli_num_rows($check) > 0) {
                echo "<script>window.history.back();</script>";
                exit;
            }
        }
        $last_invoice = mysqli_fetch_row(mysqli_query($conn, "SELECT MAX(invoice_no) FROM purchase_invoice"));
        $next_invoice = ($last_invoice[0]) + 1;
    }

    $first_insert_id = 0;

    foreach ($books as $key => $book_id) {
        $price = $prices[$key];
        $qty = $quantities[$key];
        $discount = $discounts[$key];
        $total_price = $totals[$key];
        $net = $net_prices[$key];

        $radioName = "discount_type_" . $key;
        $discountType = isset($_POST[$radioName]) ? $_POST[$radioName] : 'percent';
        $discountTypeValue = ($discountType === 'percent') ? 1 : 0;

        $sql = "INSERT INTO purchase_invoice
            (invoice_no, vendor_id, invoice_date, book_id, price, quantity, discount, discount_type, total_price, net_price, status_invoice)
            VALUES
            ('$next_invoice', '$vendor_id', '$pub_date', '$book_id', '$price', '$qty', '$discount', '$discountTypeValue', '$total_price', '$net', '$status_invoice')";
        if (!$conn->query($sql)) {
            die('SQL Error: ' . $conn->error);
        }

        // Only update stock if saving (not draft)
        if (!$is_draft) {
            $updateStock = "UPDATE books SET quantity = quantity + $qty WHERE id = '$book_id'";
            if (!$conn->query($updateStock)) {
                die('Stock Update Error: ' . $conn->error);
            }
        }

        if ($first_insert_id == 0) {
            $first_insert_id = $conn->insert_id;
        }
    }

    // Only process transactions if saving (not draft)
    if (!$is_draft) {
        $balanceQuery = mysqli_query($conn, "
            SELECT COALESCE(SUM(debit_amount), 0) - COALESCE(SUM(credit_amount), 0) AS balance
            FROM vendor_transactions WHERE vendor_id = '$vendor_id'
        ");
        $balanceRow = mysqli_fetch_assoc($balanceQuery);
        $previous_total = $balanceRow ? $balanceRow['balance'] : 0;
        $new_total = $previous_total + $grand_total;

        $insertTrans = "INSERT INTO vendor_transactions
            (invoice_no, vendor_id, total_amount, debit_amount, credit_amount, tdate)
            VALUES ('$next_invoice', '$vendor_id', '$new_total', '$grand_total', '0', '$pub_date')";
        if (!$conn->query($insertTrans)) {
            die('Transaction Error: ' . $conn->error);
        }

        $updateVendor = "UPDATE vendor SET total_amount = total_amount + $grand_total WHERE id = '$vendor_id'";
        if (!$conn->query($updateVendor)) {
            die('Vendor Update Error: ' . $conn->error);
        }

        echo "<script>window.open('./receipt.php?id=$next_invoice','_self')</script>";
        exit;
    } else {
        echo "<script>alert('Draft saved successfully!'); window.open('./book.php','_self');</script>";
        exit;
    }
}

// Load draft data if editing
$draft_data_p = null;
$draft_items_p = [];
if (isset($_GET['draft_id'])) {
    $draft_invoice_no = mysqli_real_escape_string($conn, $_GET['draft_id']);
    $draft_query = mysqli_query($conn, "
        SELECT pi.*, v.company_name AS vendor_name, b.title AS book_title
        FROM purchase_invoice pi
        LEFT JOIN vendor v ON pi.vendor_id = v.id
        LEFT JOIN books b ON pi.book_id = b.id
        WHERE pi.invoice_no = '$draft_invoice_no' AND pi.status_invoice = 0
        ORDER BY pi.id ASC
    ");
    while ($row = mysqli_fetch_assoc($draft_query)) {
        $draft_items_p[] = $row;
    }
    if (!empty($draft_items_p)) {
        $draft_data_p = $draft_items_p[0];
    }
}
?>

<style>
    .form-control:focus {
        border: 1.5px solid #045E70 !important;
        box-shadow: 0 0 12px rgba(4, 94, 112, 0.8) !important;
        background-color: #f0fcff !important;
        outline: none !important;
    }
    body { background: #e5f4f9; font-family: 'Open Sans', sans-serif; }
    .card-premium { border-radius: 15px; border: none; box-shadow: 0 6px 20px rgba(0,0,0,0.08); background: #ffffff; }
    .field-group { border: 1px solid #d1d8dd; border-radius: 15px; padding: 35px; margin-bottom: 15px; background: #f9fcff; position: relative; }
    .form-label { font-weight: bold; font-size: 0.875rem; color: #0890A6; }
    .page-title { font-size: 1.5rem; font-weight: bold; color: #045E70; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
    .form-control, input, select, textarea { border-radius: 5px !important; border: 1px solid #045E70; font-size: 0.9rem; transition: border 0.3s, box-shadow 0.3s; }
    .btn { border-radius: 12px !important; font-weight: 500; padding: 8px 20px; transition: 0.3s; }
    .btn:hover { opacity: 0.9; }
    #addRow { background-color: #045E70; color: #ffffff; font-weight: 500; border-radius: 12px; }
    button[name="save"] { background-color: #045E70; color: #ffffff; font-weight: 500; border-radius: 12px; padding: 6px 18px !important; font-size: 0.9rem; }
    .removeRow { position: absolute; top: 10px; right: 10px; font-size: 0.9rem; border-radius: 50%; padding: 6px 10px; line-height: 1; }
    .total, #net_price { background: #f1f6f9; font-weight: 600; text-align: right; border-radius: 10px; }
    .result-box { position: absolute; top: 100%; left: 0; right: 0; border: 1px solid #045E70; border-radius: 12px; background: #ffffff; max-height: 180px; overflow-y: auto; z-index: 999; display: none; }
    .result-box .list-group-item { padding: 6px 10px !important; font-size: 0.85rem; cursor: pointer; border-radius: 8px; }
    .result-box .list-group-item:hover { background-color: #e0f2f7; }
    .bookResults { position: absolute; top: 100%; left: 0; width: 100%; border: 1px solid #045E70; border-radius: 8px; background: #fff; max-height: 180px; overflow-y: auto; z-index: 999; }
    .active-item { background: #0890A6 !important; color: white !important; }
    /* Highlight fields on invalid submit */
    .field-invalid { border-color: #dc3545 !important; box-shadow: 0 0 6px rgba(220,53,69,0.3) !important; }
</style>

<div class="container py-4">
    <div class="page-title">Purchases Invoice</div>
    <div class="card card-premium">
        <div class="card-body">
            <br>
            <form method="post" id="purchaseForm">
                <?php if ($draft_data_p): ?>
                    <input type="hidden" name="edit_draft_invoice" value="<?= $draft_data_p['invoice_no'] ?>">
                <?php endif; ?>
                <div class="row g-3 mb-4" style="justify-content: center;">
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <input type="text" class="form-control date-picker" name="pub_date" value="<?= $draft_data_p ? date('d-m-Y', strtotime($draft_data_p['invoice_date'])) : date('d-m-Y') ?>" autocomplete="off">
                        <input type="hidden" name="pub_date_mysql" id="pub_date_mysql">
                    </div>
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Vendor</label>
                        <input type="text" class="form-control search-vendor" id="vendorInput" placeholder="Search Vendor" autocomplete="off" value="<?= $draft_data_p ? htmlspecialchars($draft_data_p['vendor_name']) : '' ?>">
                        <input type="hidden" name="vendor_id" class="vendor-id" value="<?= $draft_data_p ? $draft_data_p['vendor_id'] : '' ?>">
                        <div class="vendorResults result-box w-100"></div>
                    </div>
                </div>

                <!-- Product Rows -->
                <div id="productRows">
<?php
$p_rows = !empty($draft_items_p) ? $draft_items_p : [null];
foreach ($p_rows as $idx => $di):
    $dt = $di ? ($di['discount_type'] == 1 ? 'percent' : 'cash') : 'percent';
?>
                    <div class="field-group productRow" style="background-color: white;">
                        <button type="button" class="btn btn-sm removeRow" style="padding: 0; border: none;">
                            <img src="assets/img/cancel.png" alt="Remove" style="width: 28px; height: 28px; object-fit: contain;">
                        </button>
                        <div class="row g-2 align-items-end">
                            <div class="col-md-auto" style="min-width: 10px;">
                                <input type="text" class="serial-no" value="<?= $idx + 1 ?>" readonly style="border: none !important; width: 20px; font-weight:600; color: #0890A6;">
                            </div>
                            <div class="col-md-4 position-relative">
                                <label class="form-label">Book</label>
                                <input type="text" class="form-control search-book" name="book_name[]" placeholder="Search Book" autocomplete="off" value="<?= $di ? htmlspecialchars($di['book_title']) : '' ?>">
                                <input type="hidden" class="book_id" name="book_id[]" value="<?= $di ? $di['book_id'] : '' ?>">
                                <div class="bookResults result-box position-absolute w-100" style="z-index: 1000;"></div>
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Price</label>
                                <input type="number" name="price[]" class="form-control price" required value="<?= $di ? $di['price'] : '' ?>">
                            </div>
                            <div class="col-md-1">
                                <label class="form-label">Qty</label>
                                <input type="number" name="quantity[]" class="form-control quantity" required value="<?= $di ? $di['quantity'] : '' ?>">
                            </div>
                            <div class="col-md-1" style="height: 1.8cm;">
                                <label class="form-label">Discount</label>
                                <input type="number" name="discount[]" class="form-control discount" value="<?= $di ? $di['discount'] : '0' ?>">
                                <div class="d-flex justify-content-start mt-1">
                                    <div class="form-check me-3">
                                        <input class="form-check-input discountType" type="radio" name="discount_type_<?= $idx ?>" value="percent" <?= $dt === 'percent' ? 'checked' : '' ?> style="border-radius: 45px !important;">
                                        <label class="form-check-label">%</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input discountType" type="radio" name="discount_type_<?= $idx ?>" value="cash" <?= $dt === 'cash' ? 'checked' : '' ?> style="border-radius: 45px !important;">
                                        <label class="form-check-label">₨</label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">Total</label>
                                <input type="text" name="total_price[]" class="form-control total" readonly value="<?= $di ? $di['total_price'] : '' ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label mb-0">Net Price</label>
                                <div class="d-flex">
                                    <input type="text" class="form-control fw-bold text-end net_price_display" readonly style="max-width: 180px;" value="<?= $di ? $di['net_price'] : '' ?>">
                                    <input type="hidden" class="net_price" name="net_price[]" value="<?= $di ? $di['net_price'] : '' ?>">
                                </div>
                            </div>
                        </div>
                    </div>
<?php endforeach; ?>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <button type="button" class="btn" id="addRow" style="background-color: #045E70; color: white;">Add Product</button>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3 ms-auto">
                        <label class="form-label fw-bold">Grand Total</label>
                        <input type="text" id="grand_total" name="grand_total" class="form-control fw-bold text-end" value="0.00" readonly>
                    </div>
                </div>
                <div class="text-end">
                    <button type="submit" class="btn px-4 me-2" name="draft" style="background:#f0ad4e;color:#fff;">Draft</button>
                    <button type="submit" class="btn px-4" name="save">Save Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function () {
    $('.date-picker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function (e) {
        var date = e.date;
        var mysqlDate = date.getFullYear() + '-' +
            ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
            ('0' + date.getDate()).slice(-2);
        $('#pub_date_mysql').val(mysqlDate);
    });

    var today = new Date();
    var mysqlToday = today.getFullYear() + '-' +
        ('0' + (today.getMonth() + 1)).slice(-2) + '-' +
        ('0' + today.getDate()).slice(-2);
    $('#pub_date_mysql').val(mysqlToday);
});

// ─── Row Counter ───────────────────────────────────────────────
let rowCounter = 0;

const createRowTemplate = () => {
    rowCounter++;
    return `
<div class="field-group productRow" style="background-color: white;">
  <button type="button" class="btn btn-sm removeRow" style="padding: 0; border: none;">
    <img src="assets/img/cancel.png" alt="Remove" style="width: 28px; height: 28px; object-fit: contain;">
  </button>
  <div class="row g-2 align-items-end">
    <div class="col-md-auto" style="min-width: 10px;">
      <input type="text" class="serial-no" value="1" readonly style="border: none !important; width: 20px; font-weight:600; color: #0890A6;">
    </div>
    <div class="col-md-4 position-relative">
      <label class="form-label">Book</label>
      <input type="text" class="form-control search-book" name="book_name[]" placeholder="Search Book" autocomplete="off">
      <input type="hidden" class="book_id" name="book_id[]">
      <div class="bookResults result-box position-absolute w-100" style="z-index:1000;"></div>
    </div>
    <div class="col-md-1">
      <label class="form-label">Price</label>
      <input type="number" name="price[]" class="form-control price" required>
    </div>
    <div class="col-md-1">
      <label class="form-label">Qty</label>
      <input type="number" name="quantity[]" class="form-control quantity" required>
    </div>
    <div class="col-md-1" style="height: 1.8cm;">
      <label class="form-label">Discount</label>
      <input type="number" name="discount[]" class="form-control discount" value="0">
      <div class="d-flex justify-content-start mt-1">
        <div class="form-check me-3">
          <input class="form-check-input discountType" type="radio" name="discount_type_${rowCounter}" value="percent" checked style="border-radius:45px !important;">
          <label class="form-check-label">%</label>
        </div>
        <div class="form-check">
          <input class="form-check-input discountType" type="radio" name="discount_type_${rowCounter}" value="cash" style="border-radius:45px !important;">
          <label class="form-check-label">₨</label>
        </div>
      </div>
    </div>
    <div class="col-md-2">
      <label class="form-label">Total</label>
      <input type="text" name="total_price[]" class="form-control total" readonly>
    </div>
    <div class="col-md-2">
      <label class="form-label mb-0">Net Price</label>
      <input type="text" class="form-control fw-bold text-end net_price_display" readonly style="max-width: 180px;">
      <input type="hidden" class="net_price" name="net_price[]">
    </div>
  </div>
</div>`;
};

// ─── Calculations ──────────────────────────────────────────────
function calculateRow(row) {
    let price    = parseFloat($(row).find('.price').val()) || 0;
    let qty      = parseFloat($(row).find('.quantity').val()) || 0;
    let discount = parseFloat($(row).find('.discount').val()) || 0;
    let total    = price * qty;

    $(row).find('.total').val(total.toFixed(2));

    let type = $(row).find('.discountType:checked').val() || 'percent';
    let net  = (type === 'percent') ? total - (total * discount / 100) : total - discount;
    if (net < 0) net = 0;

    $(row).find('.net_price').val(net.toFixed(2));
    $(row).find('.net_price_display').val(net.toFixed(2));

    calculateGrandTotal();
}

function calculateGrandTotal() {
    let grandTotal = 0;
    $(".productRow").each(function () {
        grandTotal += parseFloat($(this).find(".net_price").val()) || 0;
    });
    $("#grand_total").val(grandTotal.toFixed(2));
}

$(document).on("input change", ".price, .quantity, .discount, .discountType", function () {
    calculateRow($(this).closest(".productRow"));
});

// ─── Serial Numbers ────────────────────────────────────────────
function updateSerialNumbers() {
    $(".productRow").each(function (index) {
        $(this).find(".serial-no").val(index + 1);
    });
}

// ─── Add / Remove Rows ─────────────────────────────────────────
$("#addRow").on("click", function () {
    $("#productRows").append(createRowTemplate());
    updateSerialNumbers();
    calculateGrandTotal();
});

$(document).on("click", ".removeRow", function () {
    $(this).closest(".productRow").remove();
    updateSerialNumbers();
    calculateGrandTotal();
});

// ─── Enter on Discount → Add new row ──────────────────────────
$(document).on("keydown", ".discount", function (e) {
    if (e.key === "Enter") {
        e.preventDefault();
        $("#addRow").trigger("click");
        $("#productRows .productRow").last().find(".search-book").focus();
    }
});

// ─── Vendor Search ─────────────────────────────────────────────
// Track whether vendor was properly selected from dropdown
let vendorSelected = false;

$(document).on("input", ".search-vendor", function () {
    // User is typing manually → vendor no longer confirmed selected
    vendorSelected = false;
    $(".vendor-id").val("");

    let input = $(this);
    let query = input.val().trim();
    let resultsBox = input.siblings(".vendorResults");

    // Reset selected index when user types
    input.data("selectedIndex", -1);

    if (query.length > 0) {
        $.post("fetch_vendor.php", { search: query }, function (data) {
            resultsBox.html(data).show();
            resultsBox.find(".vendor-item").removeClass("active-item");
        });
    } else {
        resultsBox.hide();
    }
});

// Select vendor from dropdown
$(document).on("click", ".vendor-item", function (e) {
    e.preventDefault();
    $(".search-vendor").val($(this).text()).removeClass('field-invalid');
    $(".vendor-id").val($(this).data("id"));
    $(".vendorResults").hide();
    vendorSelected = true;
    // Move focus to first book field
    $("#productRows .productRow").first().find(".search-book").focus();
});

// Keyboard nav for vendor dropdown
$(document).on("keydown", ".search-vendor", function (e) {
    let input  = $(this);
    let box    = input.siblings(".vendorResults");
    let items  = box.find(".vendor-item");

    if (input.data("selectedIndex") === undefined) input.data("selectedIndex", -1);
    let index = input.data("selectedIndex");

    if (e.key === "Tab") {
        if (index >= 0) {
            e.preventDefault();
            $(items[index]).trigger("click");
            input.data("selectedIndex", -1);
        } else {
            box.hide();
        }
        return;
    }

    if (items.length === 0 || box.is(":hidden")) return;

    switch (e.key) {
        case "ArrowDown":
            e.preventDefault();
            index = (index + 1) % items.length;
            input.data("selectedIndex", index);
            items.removeClass("active-item").eq(index).addClass("active-item");
            items[index].scrollIntoView({ block: 'nearest' });
            break;
        case "ArrowUp":
            e.preventDefault();
            index = (index - 1 + items.length) % items.length;
            input.data("selectedIndex", index);
            items.removeClass("active-item").eq(index).addClass("active-item");
            items[index].scrollIntoView({ block: 'nearest' });
            break;
        case "Enter":
            e.preventDefault();
            if (index < 0) index = 0;
            $(items[index]).trigger("click");
            input.data("selectedIndex", -1);
            break;
        case "Escape":
            box.hide();
            input.data("selectedIndex", -1);
            break;
    }
});

// ─── Book Search ───────────────────────────────────────────────
let bookTypingTimer;

$(document).on("input", ".search-book", function () {
    clearTimeout(bookTypingTimer);
    let input      = $(this);
    let query      = input.val().trim();
    let resultsBox = input.siblings(".bookResults");

    // Reset hidden book id when user types
    input.closest(".productRow").find(".book_id").val("");
    
    // Reset selected index when user types
    input.data("selectedIndex", -1);

    bookTypingTimer = setTimeout(function () {
        if (query.length > 0) {
            $.post("fetch_books.php", { search: query }, function (data) {
                resultsBox.html(data).show();
                resultsBox.find(".book-item").removeClass("active-item");
            });
        } else {
            resultsBox.hide();
        }
    }, 150);
});

// Select book from dropdown
$(document).on("click", ".book-item", function (e) {
    e.preventDefault();
    let box    = $(this).closest(".bookResults");
    let parent = box.closest(".position-relative");
    let row    = box.closest(".productRow");

    parent.find(".search-book").val($(this).text());
    parent.find(".book_id").val($(this).data("id"));
    box.hide();

    if ($(this).data("price")) {
        row.find(".price").val($(this).data("price")).trigger("input");
    }

    // Move focus to Price
    row.find(".price").focus();
});

// Keyboard nav for book dropdown
$(document).on("keydown", ".search-book", function (e) {
    let input  = $(this);
    let box    = input.siblings(".bookResults");
    let items  = box.find(".book-item");

    if (input.data("selectedIndex") === undefined) input.data("selectedIndex", -1);
    let index = input.data("selectedIndex");

    if (e.key === "Tab") {
        if (index >= 0) {
            e.preventDefault();
            $(items[index]).trigger("click");
            input.data("selectedIndex", -1);
        } else {
            box.hide();
        }
        return;
    }

    if (items.length === 0 || box.is(":hidden")) return;

    switch (e.key) {
        case "ArrowDown":
            e.preventDefault();
            index = (index + 1) % items.length;
            input.data("selectedIndex", index);
            items.removeClass("active-item").eq(index).addClass("active-item");
            items[index].scrollIntoView({ block: 'nearest' });
            break;
        case "ArrowUp":
            e.preventDefault();
            index = (index - 1 + items.length) % items.length;
            input.data("selectedIndex", index);
            items.removeClass("active-item").eq(index).addClass("active-item");
            items[index].scrollIntoView({ block: 'nearest' });
            break;
        case "Enter":
            e.preventDefault();
            if (index < 0) index = 0;
            $(items[index]).trigger("click");
            input.data("selectedIndex", -1);
            break;
        case "Escape":
            box.hide();
            input.data("selectedIndex", -1);
            break;
    }
});

// ─── Hide dropdowns on outside click ──────────────────────────
$(document).on("click", function (e) {
    if (!$(e.target).closest(".search-vendor, .vendorResults").length) {
        $(".vendorResults").hide();
    }
    if (!$(e.target).closest(".search-book, .bookResults").length) {
        $(".bookResults").hide();
    }
});

// ─── Form Validation (silent — no alerts) ─────────────────────
$("#purchaseForm").on("submit", function (e) {
    let vendorId = $(".vendor-id").val().trim();
    if (vendorId === "" || vendorId === "0") {
        e.preventDefault();
        $(".search-vendor").addClass("field-invalid").focus();
        return false;
    }
    $(".search-vendor").removeClass("field-invalid");
});

// Remove red border when vendor is selected
$(document).on("click", ".vendor-item", function () {
    $(".search-vendor").removeClass("field-invalid");
});
</script>

<?php include 'footer.php'; ?>