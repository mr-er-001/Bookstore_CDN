<?php include "topheader.php"; ?>
<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = (int) $_GET['id'];

    // Join books with publisher, vendor, and category
    $sql = "
        SELECT 
            books.id,
            books.isbn,
            books.title,
            books.edition,
            books.published_date,
            books.pages,
            books.language,
            books.volume,
            books.pub_short,
            books.class,
            books.purchase_price,
            books.sale_price,
            books.quantity,
            books.min_qty,
            books.discount,
            publisher.publisher_name,
            vendor.company_name,
            category.category_name
        FROM books
        LEFT JOIN publisher ON books.publisher_id = publisher.id
        LEFT JOIN vendor ON books.vendor_id = vendor.id
        LEFT JOIN category ON books.category = category.id
        WHERE books.id = $id
    ";

    $result = mysqli_query($conn, $sql);
    $book = mysqli_fetch_assoc($result);
}
?>

<div class="container mt-5 d-flex justify-content-center">
  <?php if ($book): ?>
    <div class="card shadow-lg border-0 rounded-4" style="max-width: 700px; width: 100%;">
      <div class="card-header text-white text-center rounded-top-4" style="background-color: #045E70; color: white;">
        <h4 class="mb-0">
          <i class="bi bi-book"></i> <?= htmlspecialchars($book['title']) ?>
        </h4>
      </div>

      <div class="card-body p-4">
        <ul class="list-group list-group-flush">
          <li class="list-group-item d-flex justify-content-between"><strong>ISBN:</strong> <span><?= $book['isbn'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Edition:</strong> <span><?= $book['edition'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Published Date:</strong> <span><?= $book['published_date'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Pages:</strong> <span><?= $book['pages'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Language:</strong> <span><?= $book['language'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Volume:</strong> <span><?= $book['volume'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Publisher Short Name:</strong> <span><?= $book['pub_short'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Class:</strong> <span><?= $book['class'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Publisher:</strong> <span><?= $book['publisher_name'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Vendor:</strong> <span><?= $book['company_name'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Category:</strong> <span><?= $book['category_name'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Quantity:</strong> <span><?= $book['quantity'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Minimum Qty:</strong> <span><?= $book['min_qty'] ?></span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Purchase Price:</strong> <span class="badge bg-secondary fs-6"><?= $book['purchase_price'] ?> PKR</span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Sale Price:</strong> <span class="badge bg-success fs-6"><?= $book['sale_price'] ?> PKR</span></li>
          <li class="list-group-item d-flex justify-content-between"><strong>Discount:</strong> <span class="badge bg-danger fs-6"><?= $book['discount'] ?>%</span></li>
        </ul>
      </div>

      <div class="card-footer text-center bg-light">
        <div class="d-flex justify-content-center gap-2">
          <a href="tables.php" class="btn" style="background-color: #045E70; color: white;">Back</a>
          <a href="editbook.php?id=<?= $book['id'] ?>" class="btn" style="background-color: #045E70; color: white;">Edit</a>
          <a href="deletebook.php?id=<?= $book['id'] ?>" 
             class="btn" style="background-color: #045E70; color: white;"
             onclick="return confirm('Are you sure you want to delete this book?');">
            Delete
          </a>
        </div>
      </div>
    </div>
  <?php else: ?>
    <div class="alert alert-danger text-center mt-5">
      ❌ Book not found!
    </div>
  <?php endif; ?>
</div>

<?php include "footer.php"; ?>
