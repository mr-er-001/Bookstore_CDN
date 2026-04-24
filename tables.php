<?php require 'topheader.php'; ?>
<?php if($role == 2){ ?>
<?php include "dbb.php"; ?>

<?php
// 🟢 Handle Update (when Edit Modal is submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id             = (int)$_POST['id'];
    $isbn           = trim($_POST['isbn']);
    $title          = trim($_POST['title']);
    $category       = (int)$_POST['category'];
    $purchase_price = (float)$_POST['purchase_price'];
    $sale_price     = (float)$_POST['sale_price'];
    $language       = trim($_POST['language']);
    $quantity       = (int)$_POST['quantity'];
    $min_qty        = isset($_POST['min_qty']) ? (int)$_POST['min_qty'] : 0; // ✅ handles 0 correctly
    $discount       = (float)$_POST['discount'];

    if ($id > 0 && $title !== '') {
        $sql = "UPDATE books 
                SET isbn=?, title=?, category=?, purchase_price=?, sale_price=?, language=?, quantity=?, min_qty=?, discount=? 
                WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param(
            "ssiddsdiii",
            $isbn,
            $title,
            $category,
            $purchase_price,
            $sale_price,
            $language,
            $quantity,
            $min_qty, // ✅ this will correctly pass 0
            $discount,
            $id
        );

        if ($stmt->execute()) {
            echo "<script>alert('Book updated successfully!');</script>";
            echo "<script>window.open('tables.php','_self');</script>";
            exit;
        } else {
            echo "<script>alert('Error updating book.');</script>";
        }
    } else {
        echo "<script>alert('Invalid input.');</script>";
    }
}

?>
<!-- Custom Font Size -->
<style>
  .table td, .table th {
    word-wrap: break-word;   /* Break long words */
    overflow-wrap: anywhere;  /* Allow wrapping anywhere if needed */
    white-space: normal;      /* Allow multiple lines */
}
table th:nth-child(2),
table td:nth-child(2) {
    max-width: 200px;  /* adjust as needed */
    white-space: normal;
    overflow-wrap: break-word;
}

  .datatable {
    font-size: 13px;   /* smaller text */
  }
  .datatable th {
    font-size: 14px; 
    padding-right: 0px !important;/* slightly bigger headers */
  }
  .datatable td .btn {
    font-size: 10px;   /* compact buttons */
    padding: 1px 4px;
  }
  .table td .btn {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  margin-right: 0px;   /* spacing between buttons */
  padding: 0;          /* remove extra padding */
}

.table td .btn img {
  width: 28px;         /* smaller size */
  height: 28px;
  object-fit: contain;
}

.table td {
  white-space: nowrap; /* prevent wrapping inside action column */
}

</style>

<!-- Table Section -->
<section class="section" style="background-color:#F0FDFF; padding:15px;">
  <div class="pagetitle">
  <h1>Books</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item">Tables</li>
      <li class="breadcrumb-item active">Books</li>
    </ol>
  </nav>
</div><!-- End Page Title -->

<!-- Button aligned right -->
<div class="d-flex justify-content-end mt-2 me-2" style="margin-bottom: 10px;">
  <a href="bookinsert.php" class="btn btn-md" style="background-color: #045E70; color: white;">
    New Book
  </a>
</div>

  <div class="table-responsive">
    <table class="table table-hover align-middle datatable"
           style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6; border-radius:0;">
      <thead>
        <tr>
          <th style="background-color: #045E70; color: white;">ISBN</th>
          <th style="background-color: #045E70; color: white;">Title</th>
          <th style="background-color: #045E70; color: white;">Category</th>
          <th style="background-color: #045E70; color: white;">Purchase Price</th>
          <th style="background-color: #045E70; color: white;">Sale Price</th>
          <th style="background-color: #045E70; color: white;">Language</th>
          <th style="background-color: #045E70; color: white;">Quantity</th>
          <th style="background-color: #045E70; color: white;">Min Qty</th>

          <th style="background-color: #045E70; color: white;">Discount</th>
          <th style="background-color: #045E70; color: white;">Actions</th>
        </tr>
      </thead>
      <tbody>
<?php
include "dbb.php"; 
$sql = "
  SELECT 
    books.id,
    books.isbn,
    books.title,
    books.category AS category_id,
    books.purchase_price,
    books.sale_price,
    books.language,
    books.quantity,
    books.min_qty,
    books.discount,
    category.category_name AS category
  FROM books
  LEFT JOIN category ON books.category = category.id
";


$result = mysqli_query($conn, $sql);

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>
            <td>".htmlspecialchars($row['isbn'])."</td>
            <td>".htmlspecialchars($row['title'])."</td>
            <td>".htmlspecialchars($row['category'])."</td>
            <td>".htmlspecialchars($row['purchase_price'])."</td>
            <td>".htmlspecialchars($row['sale_price'])."</td>
            <td>".htmlspecialchars($row['language'])."</td>
            <td>".htmlspecialchars($row['quantity'])."</td>
            <td>".htmlspecialchars($row['min_qty'])."</td>

            
            <td>".htmlspecialchars($row['discount'])."</td>
            <td>
               <a href='viewbook.php?id={$row['id']}' class='btn'>
                        <img src='assets/img/view.png' alt='Edit''>
                      </a>
             <button class='btn edit-btn'
  data-id='{$row['id']}'
  data-isbn='".htmlspecialchars($row['isbn'])."'
  data-title='".htmlspecialchars($row['title'])."'
  data-category='{$row['category_id']}'
  data-purchase='".htmlspecialchars($row['purchase_price'])."'
  data-sale='".htmlspecialchars($row['sale_price'])."'
  data-language='".htmlspecialchars($row['language'])."'
  data-quantity='".htmlspecialchars($row['quantity'])."'
  data-min='".htmlspecialchars($row['min_qty'])."'
  data-discount='".htmlspecialchars($row['discount'])."'>
  <img src='assets/img/edit.png' alt='Edit'>
</button>


              <a href='deletebook.php?id={$row['id']}' class='btn'
                 onclick='return confirm(\"Are you sure you want to delete this book?\");'>
                <img src='assets/img/delete.png' alt='Delete' >
              </a>
            </td>
          </tr>";
  }
} else {
  echo "<tr><td colspan='9' class='text-center text-muted'>No books found</td></tr>";
}
?>

      </tbody>
    </table>
  </div>
</section>

</main><!-- End #main -->
<!-- ✅ Edit Modal -->
<div class="modal fade" id="editBookModal" tabindex="-1" aria-labelledby="editBookLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editBookForm" method="POST" action="">
        <div class="modal-header" style="background-color:#045E70;color:white;">
          <h5 class="modal-title" id="editBookLabel">Edit Book</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="book_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">ISBN</label>
              <input type="text" class="form-control" name="isbn" id="isbn">
            </div>
            <div class="col-md-6">
              <label class="form-label">Title</label>
              <input type="text" class="form-control" name="title" id="title" required>
            </div>

            <div class="col-md-6">
              <label class="form-label">Category</label>
              <select class="form-select" name="category" id="category">
                <?php
                $catQuery = mysqli_query($conn, "SELECT id, category_name FROM category");
                while($cat = mysqli_fetch_assoc($catQuery)){
                    echo "<option value='{$cat['id']}'>".htmlspecialchars($cat['category_name'])."</option>";
                }
                ?>
              </select>
            </div>

            <div class="col-md-6">
              <label class="form-label">Language</label>
              <input type="text" class="form-control" name="language" id="language">
            </div>

            <div class="col-md-6">
              <label class="form-label">Purchase Price</label>
              <input type="number" step="0.01" class="form-control" name="purchase_price" id="purchase_price">
            </div>
            <div class="col-md-6">
              <label class="form-label">Sale Price</label>
              <input type="number" step="0.01" class="form-control" name="sale_price" id="sale_price">
            </div>

            <div class="col-md-6">
              <label class="form-label">Quantity</label>
              <input type="number" class="form-control" name="quantity" id="quantity">
            </div>
            <div class="col-md-6">
              <label class="form-label">Discount (%)</label>
              <input type="number" class="form-control" name="discount" id="discount">
            </div>
              <div class="col-md-6">
  <label class="form-label">Minimum Quantity</label>
  <input type="number" class="form-control" name="min_qty" id="min_qty">
</div>
          </div>
        </div>

      


        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn" style="background-color:#045E70;color:white;">Save Changes</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>


$(document).ready(function() {
  // 🟦 Open modal and fill data
  $(document).on("click", ".edit-btn", function() {
  $("#book_id").val($(this).data("id"));
  $("#isbn").val($(this).data("isbn"));
  $("#title").val($(this).data("title"));
  $("#category").val($(this).data("category"));
  $("#purchase_price").val($(this).data("purchase"));
  $("#sale_price").val($(this).data("sale"));
  $("#language").val($(this).data("language"));
  $("#quantity").val($(this).data("quantity"));
  $("#min_qty").val($(this).data("min"));
  $("#discount").val($(this).data("discount"));

    const modalEl = document.getElementById('editBookModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
});
</script>

<?php  } else {
     echo "<script>window.open('index.php','_self')</script>";
} ?>




<!-- ======= Footer ======= -->
<?php require "footer.php"; ?>
