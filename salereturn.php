<?php
include 'topheader.php';
include 'dbb.php';

if (isset($_POST['save'])) {
    $return_date   = !empty($_POST['pub_date_mysql']) ? $_POST['pub_date_mysql'] : date('Y-m-d');
    $client_id     = (int)$_POST['client_id'];
    $booksSel      = $_POST['book_id'];
    $quantities    = $_POST['quantity'];
    $prices        = $_POST['price'];
    $discounts     = $_POST['discount'];
    $discount_types= $_POST['discount_type'];
    $totals        = $_POST['total_price'];
    $net_prices    = $_POST['net_price'];
    $grand_total   = (float)$_POST['grand_total'];

    // Get next return number
    $last_return_row = mysqli_fetch_row(mysqli_query($conn, "SELECT MAX(return_no) FROM sale_return_invoice"));
    $next_return = ($last_return_row[0]) ? ($last_return_row[0] + 1) : 1;

    foreach ($booksSel as $key => $book_id) {
        $book_id       = (int)$book_id;
        $qty           = (int)$quantities[$key];
        $price         = (float)$prices[$key];
        $discount      = (float)$discounts[$key];
        $discount_type = isset($discount_types[$key]) ? (int)$discount_types[$key] : 0;

        // Recalculate total and net server-side
        $total_price = $price * $qty;
        if ($discount_type === 1) {
            $net_price = $total_price - ($total_price * $discount / 100);
        } else {
            $net_price = $total_price - $discount;
        }
        if ($net_price < 0) $net_price = 0;

        // Find the most recent sale for this client and book
        $saleQuery = mysqli_query($conn, "
            SELECT * FROM sale_invoice 
            WHERE client_id='{$client_id}' AND book_id='{$book_id}'
            ORDER BY invoice_no DESC LIMIT 1
        ");

        // Default invoice_no to 0 if no previous sale found
        $invoice_no = 0;

        if (mysqli_num_rows($saleQuery) > 0) {
            $saleRow    = mysqli_fetch_assoc($saleQuery);
            $invoice_no = $saleRow['invoice_no'];

            // Deduct returned qty (no restriction — can go negative)
            $origSaleQty        = (int)$saleRow['quantity'];
            $newQty             = $origSaleQty - $qty;
            $sale_price         = (float)$saleRow['price'];
            $sale_discount      = (float)$saleRow['discount'];
            $sale_discount_type = isset($saleRow['discount_type']) ? (int)$saleRow['discount_type'] : 0;

            $new_total = $sale_price * $newQty;
            if ($sale_discount_type === 1) {
                $new_net = $new_total - ($new_total * $sale_discount / 100);
            } else {
                $new_net = $new_total - $sale_discount;
            }
            if ($new_net < 0) $new_net = 0;

            // Update sale_invoice
            mysqli_query($conn, "
                UPDATE sale_invoice
                SET quantity = {$newQty},
                    total_price = {$new_total},
                    net_price = {$new_net}
                WHERE id = {$saleRow['id']}
            ");
        }

        // Always update stock
        mysqli_query($conn, "
            UPDATE books 
            SET quantity = quantity + {$qty} 
            WHERE id = '{$book_id}'
        ");

        // Always insert return record
        $insertReturn = mysqli_query($conn, "
            INSERT INTO sale_return_invoice 
                (return_no, invoice_no, client_id, book_id, quantity, price, discount, discount_type, total_price, net_price, return_date)
            VALUES 
                ('{$next_return}', '{$invoice_no}', '{$client_id}', '{$book_id}', '{$qty}', '{$price}', '{$discount}', '{$discount_type}', '{$total_price}', '{$net_price}', '{$return_date}')
        ");

        if (!$insertReturn) {
            die('❌ Return Insert Error: ' . mysqli_error($conn));
        }
    }

    // Update client balance
    mysqli_query($conn, "
        UPDATE client 
        SET total_amount = total_amount - {$grand_total}
        WHERE id = '{$client_id}'
    ");

    $clientRes  = mysqli_query($conn, "SELECT total_amount FROM client WHERE id = '{$client_id}'");
    $clientRow  = mysqli_fetch_assoc($clientRes);
    $updatedTotal = isset($clientRow['total_amount']) ? $clientRow['total_amount'] : 0;

    // Insert transaction record
    $cash_mode = 1;
    mysqli_query($conn, "
        INSERT INTO client_transactions 
            (invoice_no, client_id, total_amount, debit_amount, credit_amount, cash_mode, tdate)
        VALUES 
            ('{$next_return}', '{$client_id}', '{$updatedTotal}', '0', '{$grand_total}', '{$cash_mode}', '{$return_date}')
    ");

    // Redirect to invoice page
    echo "<script>window.open('./sale_return_invoice.php?id={$next_return}','_self')</script>";
    exit;
}
?>



<style>
    .field-group .form-control {
    padding: 4px 6px;
    font-size: 1rem;
}
.field-group label {
    font-size: 0.75rem;
    margin-bottom: 2px;
}
.field-group .form-check-inline {
    margin-right: 5px;
}

body { background: #e5f4f9; font-family: 'Open Sans', sans-serif; }
.card-premium { border-radius: 15px; border: none; box-shadow: 0 6px 20px rgba(0,0,0,0.08); background: #fff; }
.card-premium-header { background: #045E70; color: #fff; font-weight: bold; font-size: 1.25rem; padding: 12px 20px; border-top-left-radius: 15px; border-top-right-radius: 15px; }
.field-group { border: 1px solid #d1d8dd; border-radius: 15px; padding: 30px; margin-bottom: 15px; background: #f9fcff; position: relative; }
.form-label { font-weight: bold; font-size: 0.875rem; color: #0890A6; }
.page-title { font-size: 1.5rem; font-weight: bold; color: #045E70; margin-bottom: 15px; display: flex; align-items: center; gap: 8px; }
.form-control, input, select, textarea { border-radius: 5px !important; border: 1px solid #045E70; font-size: 0.9rem; transition: border 0.3s, box-shadow 0.3s; }
.form-control:focus { border-color: #045E70; box-shadow: 0 0 6px rgba(4,94,112,0.3); outline: none; }
.btn { border-radius: 12px !important; font-weight: 500; padding: 8px 20px; transition: 0.3s; }
.btn:hover { opacity: 0.9; }
#addRow { background-color: #045E70; color: #fff; font-weight: 500; }
button[name="save"] { background-color: #045E70; color: #fff; font-weight: 500; padding: 6px 18px !important; font-size: 0.9rem; border-radius: 12px; }
.result-box { position: absolute; top: 100%; left: 0; right: 0; border: 1px solid #045E70; border-radius: 12px; background: #fff; max-height: 180px; overflow-y: auto; z-index: 999; display: none; }
.result-box .list-group-item { padding: 6px 10px !important; font-size: 0.85rem; cursor: pointer; border-radius: 8px; }
.result-box .list-group-item:hover { background-color: #e0f2f7; }
.total, .net_price, #net_price { background: #f1f6f9; font-weight: 600; text-align: right; border-radius: 10px; }
.field-group .removeRow { position: absolute; top: 10px; right: 10px; width: 32px; height: 32px; border-radius: 8px; display: flex; align-items: center; justify-content: center; }
/* Highlight client field if empty on submit attempt */
.client-invalid { border-color: #dc3545 !important; box-shadow: 0 0 6px rgba(220,53,69,0.3) !important; }
</style>

<div class="container py-4">
    <!-- 🔹 Header styled like Purchases Invoice -->
    <div class="page-title">
        Sale Return
    </div>

    <div class="card card-premium">
        <div class="card-body"><br>
            <form method="post" id="saleReturnForm">
                <!-- Top Row: Date & Client -->
                <div class="row g-3 mb-4" style="justify-content: center;">
                    <div class="col-md-4">
                        <label class="form-label">Date</label>
                        <!-- ✅ User sees dd-mm-yyyy, DB gets yyyy-mm-dd -->
                        <input type="text" class="form-control date-picker" name="pub_date" 
                               value="<?= date('d-m-Y') ?>" autocomplete="off">
                        <input type="hidden" name="pub_date_mysql" id="pub_date_mysql">
                    </div>
                    <div class="col-md-4 position-relative">
                        <label class="form-label">Client</label>
                        <input type="text" class="form-control search-client" placeholder="Search Client" autocomplete="off">
                        <input type="hidden" class="client-id" name="client_id">
                        <div class="clientResults result-box w-100"></div>
                    </div>
                </div>

                <!-- Product Rows -->
                <div id="productRows">
  <div class="field-group productRow">
    <div class="row g-2 align-items-end">
        <div class="col-md-4 position-relative">
            <label class="form-label">Book</label>
            <input type="text" class="form-control search-book" placeholder="Search Book" autocomplete="off">
            <input type="hidden" class="book-id" name="book_id[]">
            <div class="bookResults result-box w-100"></div>
        </div>
        <div class="col-md-1">
            <label class="form-label">Qty</label>
            <input type="number" class="form-control qty" name="quantity[]" required>
        </div>
        <?php $uid = uniqid(); ?>
<div class="col-md-1" style="height: 1.6cm;">
  <label class="form-label">Discount</label>
  <input type="number" name="discount[]" class="form-control discount" value="0" required>

  <!-- Radio buttons moved below input -->
  <div class="d-flex justify-content-start mt-1">
    <div class="form-check me-3">
      <input class="form-check-input discountType" 
             type="radio" 
             name="discount_type_<?= $uid ?>" 
             value="percent" 
             checked 
             style="border-radius: 45px !important;">
      <label class="form-check-label">%</label>
    </div>
    <div class="form-check">
      <input class="form-check-input discountType" 
             type="radio" 
             name="discount_type_<?= $uid ?>" 
             value="cash" 
             style="border-radius: 45px !important;">
      <label class="form-check-label">₨</label>
    </div>
  </div>

  <!-- ✅ Hidden input that stores actual selected type -->
  <input type="hidden" name="discount_type[]" class="discount-type-hidden" value="percent">
</div>
        <div class="col-md-1">
            <label class="form-label">Price</label>
            <input type="number" class="form-control price" name="price[]" readonly>
        </div>
        
        <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="number" class="form-control total" name="total_price[]" readonly>
        </div>
      

        <div class="col-md-2">
            <label class="form-label">Net</label>
            <input type="number" class="form-control net_price" name="net_price[]" readonly>
        </div>
        <div class="col-md-1 d-flex justify-content-center">
            <button type="button" class="btn btn-sm  removeRow" style="padding: 0; border: none;">
    <img src="assets/img/cancel.png" alt="Remove" style="width: 28px; height: 28px; object-fit: contain;">
  </button>
        </div>
    </div>
</div>


                </div>

                <div class="d-flex justify-content-between align-items-center mb-3 mt-4">
                    <button type="button" class="btn" id="addRow" style="background:#045E70;color:#fff;">Add Book</button>
                    <div class="text-end">
                        <label class="form-label">Grand Total</label>
                        <input type="text" id="net_price" name="grand_total" class="form-control fw-bold text-end" readonly>
                    </div>
                </div>
    
                <div class="text-end mt-3">
                    <button type="submit" name="save" class="btn" style="background:#045E70;color:#fff;">Save Invoice</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ✅ jQuery + Datepicker -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>

<script>
$(document).ready(function() {
    // ✅ Initialize datepicker
    $('.date-picker').datepicker({
        format: 'dd-mm-yyyy',
        autoclose: true,
        todayHighlight: true
    }).on('changeDate', function(e) {
        var date = e.date;
        var mysqlDate = date.getFullYear() + '-' +
                        ('0' + (date.getMonth() + 1)).slice(-2) + '-' +
                        ('0' + date.getDate()).slice(-2);
        $('#pub_date_mysql').val(mysqlDate);
    });

    // Set today's date in MySQL format
    var today = new Date();
    var mysqlToday = today.getFullYear() + '-' +
                     ('0' + (today.getMonth() + 1)).slice(-2) + '-' +
                     ('0' + today.getDate()).slice(-2);
    $('#pub_date_mysql').val(mysqlToday);
});

// JS logic remains same as before
let rowTemplate = $('.productRow').first().clone();
let rowCount = 1;

// Calculate row totals
function calculateRow(row){
    let price = parseFloat($(row).find('.price').val()) || 0;
    let qty = parseFloat($(row).find('.qty').val()) || 0;
    let discountInput = $(row).find('.discount').val();
    let discount = discountInput === "" ? 0 : parseFloat(discountInput);
    if (isNaN(discount)) discount = 0;

    let total = price * qty;

    // Get selected discount type safely
    let type = $(row).find('.discountType:checked').val();
    if (!type) type = 'percent'; // default fallback

    // Apply discount correctly
    let net;
    if (type === 'percent') {
        net = total - (total * discount / 100);
    } else {
        net = total - discount;
    }

    if (net < 0) net = 0;

    // Update fields
    $(row).find('.total').val(total.toFixed(2));
    $(row).find('.net_price').val(net.toFixed(2));

    // Update grand total
    calculateGrandTotal();
}

// Fix live typing in discount (recalculate properly)
$(document).on('input', '.discount', function(){
    let row = $(this).closest('.productRow');
    calculateRow(row);
});


// Calculate grand total
function calculateGrandTotal(){
    let total = 0;
    $('.productRow').each(function(){ total += parseFloat($(this).find('.net_price').val()) || 0; });
    $('#net_price').val(total.toFixed(2));
}

// Auto calculate
$(document).on('input change', '.qty, .discount, .discountType', function(){ calculateRow($(this).closest('.productRow')); });

// Update hidden discount type
$(document).on('change', '.discountType', function(){
    let row = $(this).closest('.productRow');
    row.find('.discount-type-hidden').val(row.find('.discountType:checked').val());
    calculateRow(row);
});

// Client search
$(document).on('keyup','.search-client', function(){
    let input = $(this); 
    let query = input.val();
    let resultsBox = input.siblings('.clientResults');
    // Clear hidden client_id if user is typing again
    $('.client-id').val('');
    input.removeClass('client-invalid');
    if(query.length>0){ 
        $.post('fetch_client.php',{search:query}, function(data){ resultsBox.html(data).show(); }); 
    } else { resultsBox.hide(); }
});
$(document).on('click','.client-item', function(e){
    e.preventDefault();
    let item=$(this);
    $('.search-client').val(item.text());
    $('.client-id').val(item.data('id'));
    $('.clientResults').hide();
    $('.search-client').removeClass('client-invalid');
});

// Book search
$(document).on('keyup','.search-book', function(){
    let input = $(this); 
    let query = input.val();
    let resultsBox = input.siblings('.bookResults');
    if(query.length>0){ 
        $.post('fetch_books.php',{search:query}, function(data){ resultsBox.html(data).show(); }); 
    } else { resultsBox.hide(); }
});
$(document).on('click','.book-item', function(e){
    e.preventDefault();
    let item = $(this);
    let parent = item.closest('.col-md-4');
    parent.find('.search-book').val(item.text());
    parent.find('.book-id').val(item.data('id'));
    parent.find('.bookResults').hide();

    let row = parent.closest('.productRow');
    row.find('.price').val(item.data('price'));
    row.find('.current_qty').val(item.data('stock'));
    row.find('.discount').val(item.data('discount'));
    calculateRow(row[0]);
});

// Add new product row
$('#addRow').click(function(){
    let newRow = $('.productRow').first().clone();

    // Clear inputs
    newRow.find('input').each(function(){
        if($(this).is('[type=radio]')){
            $(this).prop('checked', $(this).val() === 'percent'); // default to %
        } else {
            $(this).val(''); // clear text/number inputs
        }
    });

    // Unique radio names per row
    let uniqueId = Date.now();
    newRow.find('.discountType').each(function(){
        $(this).attr('name', 'discount_type_' + uniqueId);
    });

    $('#productRows').append(newRow);
});

// Book search (works for both existing and new rows)
$(document).on('keyup', '.search-book', function() {
    let input = $(this); 
    let query = input.val();
    let resultsBox = input.siblings('.bookResults');
    if(query.length > 0){ 
        $.post('fetch_books.php', {search: query}, function(data){
            resultsBox.html(data).show();
        }); 
    } else { 
        resultsBox.hide(); 
    }
});

// Click on book item (works for both existing and new rows)
$(document).on('click', '.book-item', function(e){
    e.preventDefault();
    let item = $(this);
    let parent = item.closest('.col-md-3');
    parent.find('.search-book').val(item.text());
    parent.find('.book-id').val(item.data('id'));
    parent.find('.bookResults').hide();

    let row = parent.closest('.productRow');
    row.find('.price').val(item.data('price'));
    row.find('.current_qty').val(item.data('stock'));
    calculateRow(row);
});


// ✅ Prevent form submission if client not selected — NO alert, just highlight field
$('#saleReturnForm').on('submit', function(e) {
    // Convert hidden discount types before submit
    $('.productRow').each(function() {
        let hiddenField = $(this).find('.discount-type-hidden');
        let val = hiddenField.val();
        if (val === 'percent') hiddenField.val('1');
        else if (val === 'cash') hiddenField.val('0');
        else if (val !== '1' && val !== '0') hiddenField.val('0');
    });

    // Silent client validation — highlight field, no popup
    let clientId = $('.client-id').val().trim();
    if (clientId === "") {
        e.preventDefault();
        $('.search-client').addClass('client-invalid').focus();
        return false;
    }
});

$(document).on("keydown", ".discount", function(e) {
    if (e.key === "Enter") {
        e.preventDefault();
        $("#addRow").trigger("click");

        // Focus the first input (book) of the new row
        let newRow = $("#productRows .productRow").last();
        newRow.find("input.search-book").focus();
    }
});

$(document).on('click', '.book-item', function (e) {
    e.preventDefault();

    let item = $(this);

    // Correct parent tracing
    let row = item.closest('.bookResults')
                  .closest('.col-md-4')
                  .closest('.productRow');

    row.find('.search-book').val(item.text());
    row.find('.book-id').val(item.data('id'));
    row.find('.bookResults').hide();

    // Apply values
    row.find('.price').val(item.data('price'));
    row.find('.current_qty').val(item.data('stock'));
    row.find('.discount').val(item.data('discount'));

    calculateRow(row);
});


let typingTimer;
$(document).on("keyup", ".search-book", function() {
    clearTimeout(typingTimer);
    let input = $(this);
    let query = input.val();
    let resultsBox = input.siblings('.bookResults');
    typingTimer = setTimeout(function(){
        if(query.length > 0){
            $.post('fetch_books.php',{search:query}, function(data){
                resultsBox.html(data).show();
            });
        } else {
            resultsBox.hide();
        }
    }, 150);
});


// Track selected index for each row
$(document).on("keydown", ".search-book", function(e){
    let input = $(this);
    let box = input.siblings(".bookResults");
    let items = box.find(".book-item");
    if(items.length === 0) return;

    if(input.data('selectedIndex') === undefined) input.data('selectedIndex', -1);
    let index = input.data('selectedIndex');

    switch(e.key){
        case "ArrowDown":
            e.preventDefault();
            index = (index + 1) % items.length;
            input.data('selectedIndex', index);
            highlightBook(items, index);
            break;
        case "ArrowUp":
            e.preventDefault();
            index = (index - 1 + items.length) % items.length;
            input.data('selectedIndex', index);
            highlightBook(items, index);
            break;
        case "Enter":
            e.preventDefault();
            if(index >= 0 && index < items.length){
                selectBook(items.eq(index), input, box);
            }
            break;
        case "Escape":
            box.hide();
            input.data('selectedIndex', -1);
            break;
    }
});

function highlightBook(items, index){
    items.removeClass("active");
    items.eq(index).addClass("active");
    items.eq(index)[0].scrollIntoView({block:'nearest'});
}

function selectBook(item, input, box){
    let row = input.closest('.productRow');
    row.find('.search-book').val(item.text());
    row.find('.book-id').val(item.data('id'));
    row.find('.price').val(item.data('price'));
    row.find('.current_qty').val(item.data('stock'));
    row.find('.discount').val(item.data('discount'));
    calculateRow(row);
    box.hide();
    input.data('selectedIndex', -1);
}

</script>

<?php include 'footer.php'; ?>