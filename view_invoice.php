<?php
include 'topheader.php';
include 'dbb.php';

if (!isset($_GET['id'])) {
    die("No invoice ID provided.");
}

$id = $_GET['id'];

// Handle update
if (isset($_POST['update'])) {
    $vendor_id   = $_POST['vendor_id'];
    $book_id     = $_POST['book_id'];
    $price       = $_POST['price'];
    $quantity    = $_POST['quantity'];
    $total_price = $_POST['total_price'];
    $discount    = $_POST['discount'];
    $net_price   = $_POST['net_price'];

    $update_sql = "UPDATE purchase_invoice 
                   SET  quantity='$quantity'
                   WHERE id='$id'";
    
    if ($conn->query($update_sql) === TRUE) {
        echo "<script>alert('Invoice updated successfully!'); window.location='view_invoice.php?id=$id';</script>";
        exit;
    } else {
        echo "Error updating record: " . $conn->error;
    }
}

// Fetch invoice with joins
$sql = "SELECT pi.*, v.company_name, v.contact_name, v.mobile, b.title AS book_title
        FROM purchase_invoice pi
        LEFT JOIN vendor v ON pi.vendor_id = v.id
        LEFT JOIN books b ON pi.book_id = b.id
        WHERE pi.id = '$id'";
$result = $conn->query($sql);
$invoice = $result->fetch_assoc();

// Fetch all vendors for dropdown
$vendors_sql = "SELECT id, company_name, mobile FROM vendor";
$vendors_result = $conn->query($vendors_sql);

// Fetch all books for dropdown
$books_sql = "SELECT id, title FROM books";
$books_result = $conn->query($books_sql);
?>

<div class="container mt-5">
    <div class="card shadow-lg p-4">
        <h3 class="text-center mb-4">Invoice <?php echo $invoice['invoice_no']; ?></h3>
        
        <form method="post">
            <div class="mb-3">
                <label><strong>Vendor:</strong></label>
                <select name="vendor_id" id="vendor_id" class="form-control">
                    <?php while ($vendor = $vendors_result->fetch_assoc()) { ?>
                        <option value="<?php echo $vendor['id']; ?>" 
                            data-contact="<?php echo $vendor['mobile']; ?>"
                            <?php if ($vendor['id'] == $invoice['vendor_id']) echo 'selected'; ?>>
                            <?php echo $vendor['company_name']; ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <p><strong>Mobile No:</strong> <span id="vendor_contact"><?php echo $invoice['mobile']; ?></span></p>
            <p><strong>Date & Time:</strong> <?php echo date("d-m-Y h:i A", strtotime($invoice['invoice_date'])); ?></p>

            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Book</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Total Price</th>
                        <th>Discount</th>
                        <th>Net Price</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <select name="book_id" class="form-control">
                                <?php 
                                $books_result->data_seek(0); // Reset pointer
                                while ($book = $books_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $book['id']; ?>" 
                                        <?php if ($book['id'] == $invoice['book_id']) echo 'selected'; ?>>
                                        <?php echo $book['title']; ?>
                                    </option>
                                <?php } ?>
                            </select>
                        </td>
                        <td><input type="number" name="price" value="<?php echo $invoice['price']; ?>" class="form-control"></td>
                        <td><input type="number" name="quantity" value="<?php echo $invoice['quantity']; ?>" class="form-control"></td>
                        <td><input type="number" name="total_price" value="<?php echo $invoice['total_price']; ?>" class="form-control"></td>
                        <td><input type="number" name="discount" value="<?php echo $invoice['discount']; ?>" class="form-control"></td>
                        <td><input type="number" name="net_price" value="<?php echo $invoice['net_price']; ?>" class="form-control"></td>
                    </tr>
                </tbody>
            </table>

            <div class="text-center mt-4">
                <button type="submit" name="update" class="btn btn-success">💾 Save Changes</button>
                <button type="button" onclick="window.print()" class="btn btn-primary">🖨️ Print</button>
                <a href="book.php" class="btn btn-secondary">⬅️ Back</a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById("vendor_id").addEventListener("change", function() {
    var selected = this.options[this.selectedIndex];
    var contact = selected.getAttribute("data-contact");
    document.getElementById("vendor_contact").textContent = contact;  
});
</script>

<?php include 'footer.php'; ?>
