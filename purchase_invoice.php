<?php
include 'topheader.php';
include 'dbb.php';

// Handle Save
if (isset($_POST['save'])) {
    $invoice_no   = $_POST['isbn'];
    $vendor_id    = $_POST['vendor'];
    $pub_date     = $_POST['pub_date'];
    $book_id      = $_POST['book'];
    $price        = $_POST['price'];
    $quantity     = $_POST['quantity'];
    $total_price  = $_POST['total_price'];
    $discount     = $_POST['discount'];
    $net_price    = $_POST['net_price'];

    $sql = "INSERT INTO purchase_invoice 
            (invoice_no, vendor_id, invoice_date, book_id, price, quantity, total_price, discount, net_price)
            VALUES ('$invoice_no', '$vendor_id', '$pub_date', '$book_id', '$price', '$quantity', '$total_price', '$discount', '$net_price')";
    $conn->query($sql);
}

// Handle Quantity Update
if (isset($_POST['update_qty'])) {
    $id = $_POST['id'];
    $qty = $_POST['qty'];
    $price = $_POST['price'];
    $total_price = $qty * $price;
    $conn->query("UPDATE purchase_invoice 
                  SET quantity='$qty', total_price='$total_price', net_price=($total_price - discount) 
                  WHERE id='$id'");
}
?>

<div class="pagetitle">
    <h1>PURCHASE</h1>
</div>

<section class="section">
    <div class="row justify-content-center">
        <div class="col-lg-12">

            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-body p-4">
                    <h4 class="card-title mb-4 text-primary fw-bold border-bottom pb-2">PURCHASE</h4>

                    <!-- Purchase Form -->
                    <form method="post">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Invoice No</label>
                                <input type="text" class="form-control" name="isbn" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Vendor</label>
                                <select name="vendor" class="form-control" required>
                                    <option value="">Select Vendor</option>
                                    <?php
                                    $vendors = $conn->query("SELECT id, company_name FROM vendor");
                                    while ($v = $vendors->fetch_assoc()) {
                                        echo "<option value='{$v['id']}'>{$v['company_name']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Date</label>
                                <input type="date" class="form-control" name="pub_date">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Book</label>
                                <select name="book" class="form-control" required>
                                    <option value="">Select Book</option>
                                    <?php
                                    $books = $conn->query("SELECT id, title FROM books");
                                    while ($b = $books->fetch_assoc()) {
                                        echo "<option value='{$b['id']}'>{$b['isbn']}</option>";
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Price</label>
                                <input type="number" class="form-control" id="price" name="price">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Quantity</label>
                                <input type="number" class="form-control" id="quantity" name="quantity">
                            </div>
                        </div>

                        <div class="row g-3 mt-1">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Total Price</label>
                                <input type="text" class="form-control" id="total_price" name="total_price" readonly>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Discount (%)</label>
                                <input type="number" class="form-control" id="discount" name="discount">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Net Price</label>
                                <input type="text" class="form-control" id="net_price" name="net_price" readonly>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-success px-4" name="save">💾 Save</button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Saved Invoices Table -->
            <div class="card mt-4">
                <div class="card-body">
                    <h5 class="card-title">Saved Invoices</h5>
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Invoice No</th>
                                <th>Vendor</th>
                                <th>Book</th>
                                <th>Price</th>
                                <th>Quantity</th>
                                <th>Total Price</th>
                                <th>Discount</th>
                                <th>Net Price</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sql = "SELECT pi.*, v.company_name, b.title AS book_title
                                    FROM purchase_invoice pi
                                    LEFT JOIN vendor v ON pi.vendor_id = v.id
                                    LEFT JOIN books b ON pi.book_id = b.id
                                    ORDER BY pi.id DESC";
                            $res = $conn->query($sql);

                            while ($row = $res->fetch_assoc()) {
                                echo "<tr>
                                    <form method='post'>
                                    <td>{$row['invoice_no']}</td>
                                    <td>{$row['company_name']}</td>
                                    <td>{$row['book_title']}</td>
                                    <td><input type='number' name='price' value='{$row['price']}' readonly class='form-control'></td>
                                    <td><input type='number' name='qty' value='{$row['quantity']}' class='form-control'></td>
                                    <td>{$row['total_price']}</td>
                                    <td>{$row['discount']}</td>
                                    <td>{$row['net_price']}</td>
                                    <td>
                                        <input type='hidden' name='id' value='{$row['id']}'>
                                        <button name='update_qty' class='btn btn-primary btn-sm'>Update</button>
                                    </td>
                                    </form>
                                </tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</section>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    function calculatePrices() {
        let price = parseFloat($('#price').val()) || 0;
        let quantity = parseFloat($('#quantity').val()) || 0;
        let discount = parseFloat($('#discount').val()) || 0;

        let total = price * quantity;
        let net = total - (total * discount / 100);

        $('#total_price').val(total.toFixed(2));
        $('#net_price').val(net.toFixed(2));
    }

    $('#price, #quantity, #discount').on('input', calculatePrices);
});
</script>

<?php include 'footer.php'; ?>
