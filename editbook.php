<?php
include "dbb.php";

// Get book details
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = mysqli_query($conn, "SELECT * FROM books WHERE id=$id");
    $book = mysqli_fetch_assoc($result);
}

// Update book
if (isset($_POST['update'])) {
    $isbn           = $_POST['isbn'];
    $edition        = $_POST['edition'];
    $pub_id         = $_POST['publisher_id'];
    $pub_date       = $_POST['pub_date'];
    $auther_id      = $_POST['author'];
    $pages          = $_POST['pages'];
    $purchase_price = $_POST['purchase_price'];
    $language       = $_POST['language'];
    $quantity       = $_POST['quantity'];
    $title          = $_POST['title'];
    $volume         = $_POST['volume'];
    $pub_short      = $_POST['pub_short'];
    $min_qty        = $_POST['min_qty'];
    $discount       = $_POST['discount'];
    $vendor         = $_POST['vendor'];
    $category       = $_POST['category'];
    $class          = $_POST['class'];
    $sale_price     = $_POST['sale_price'];

    $sql = "UPDATE books SET 
        isbn='$isbn',
        edition='$edition',
        publisher_id='$pub_id',
        published_date='$pub_date',
        auther_id='$auther_id',
        pages='$pages',
        purchase_price='$purchase_price',
        language='$language',
        quantity='$quantity',
        title='$title',
        volume='$volume',
        pub_short='$pub_short',
        min_qty='$min_qty',
        discount='$discount',
        vendor_id='$vendor',
        category='$category',
        class='$class',
        sale_price='$sale_price'
        WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: tables.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Error: " . mysqli_error($conn) . "</div>";
    }
}

include "topheader.php";
?>

<div class="pagetitle">
    <h1>Edit Book</h1>
</div>

<section class="section">
  <div class="row justify-content-center">
    <div class="col-lg-12">

      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
          

          <form method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label fw-semibold">ISBN</label>
                <input type="text" class="form-control" name="isbn" value="<?= $book['isbn'] ?>" required>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Edition</label>
                <input type="text" class="form-control" name="edition" value="<?= $book['edition'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Published Date</label>
                <input type="date" class="form-control" name="pub_date" value="<?= $book['published_date'] ?>">
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Author</label>
                <input type="text" class="form-control" name="author" value="<?= $book['auther_id'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Pages</label>
                <input type="number" class="form-control" name="pages" value="<?= $book['pages'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Purchase Price</label>
                <input type="text" class="form-control" name="purchase_price" value="<?= $book['purchase_price'] ?>">
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Language</label>
                <input type="text" class="form-control" name="language" value="<?= $book['language'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Quantity</label>
                <input type="number" class="form-control" name="quantity" value="<?= $book['quantity'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Image</label>
                <input class="form-control" type="file" name="image">
                <small class="text-muted">Leave blank to keep current image</small>
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Title</label>
                <input type="text" class="form-control" name="title" value="<?= $book['title'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Volume</label>
                <input type="text" class="form-control" name="volume" value="<?= $book['volume'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Publisher Short Name</label>
                <input type="text" class="form-control" name="pub_short" value="<?= $book['pub_short'] ?>">
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Minimum Qty</label>
                <input type="number" class="form-control" name="min_qty" value="<?= $book['min_qty'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Discount</label>
                <input type="text" class="form-control" name="discount" value="<?= $book['discount'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Vendor</label>
                <select class="form-select" name="vendor">
                  <?php
                  $vendors = $conn->query("SELECT id, company_name FROM vendor");
                  while ($row = $vendors->fetch_assoc()) {
                      $selected = $book['vendor_id'] == $row['id'] ? "selected" : "";
                      echo "<option value='{$row['id']}' $selected>{$row['company_name']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Category</label>
                <select class="form-select" name="category">
                  <?php
                  $categories = $conn->query("SELECT id, category_name FROM category");
                  while ($row = $categories->fetch_assoc()) {
                      $selected = $book['category'] == $row['id'] ? "selected" : "";
                      echo "<option value='{$row['id']}' $selected>{$row['category_name']}</option>";
                  }
                  ?>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Class</label>
                <input type="text" class="form-control" name="class" value="<?= $book['class'] ?>">
              </div>
              <div class="col-md-4">
                <label class="form-label fw-semibold">Sale Price</label>
                <input type="text" class="form-control" name="sale_price" value="<?= $book['sale_price'] ?>">
              </div>
            </div>

            <div class="row g-3 mt-1">
              <div class="col-md-4">
                <label class="form-label fw-semibold">Publisher</label>
                <select class="form-select" name="publisher_id">
                  <?php
                  $publishers = $conn->query("SELECT id, publisher_name FROM publisher");
                  while ($row = $publishers->fetch_assoc()) {
                      $selected = $book['publisher_id'] == $row['id'] ? "selected" : "";
                      echo "<option value='{$row['id']}' $selected>{$row['publisher_name']}</option>";
                  }
                  ?>
                </select>
              </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn  px-4" name="update" style="background-color: #045E70; color: white;">
                Update Book
              </button>
            </div>
          </form>
        </div>
      </div>

    </div>
  </div>
</section>

<?php include "footer.php"; ?>
