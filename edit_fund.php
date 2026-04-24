<?php
include "dbb.php";

$id = $_GET['id'] ?? 0;

// fetch fund details
$result = mysqli_query($conn, "SELECT * FROM funds WHERE id='$id'");
$fund = mysqli_fetch_assoc($result);

if(!$fund) {
  die("Fund not found");
}

if(isset($_POST['update'])) {
  $title = $_POST['title'];
  $price = $_POST['price'];
  $date  = $_POST['date'];

  $sql = "UPDATE funds SET title='$title', price='$price', date='$date' WHERE id='$id'";
  if(mysqli_query($conn, $sql)) {
    header("Location: funds.php");
    exit;
  } else {
    echo "Error: " . mysqli_error($conn);
  }
}

require "topheader.php";
?>

<div class="pagetitle">
  <h1>Edit Fund</h1>
</div>

<section class="section">
  <div class="row justify-content-center">
    <div class="col-lg-6">
      <div class="card">
        <div class="card-body">
          <h5 class="card-title">Update Fund</h5>

          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Title</label>
              <input type="text" name="title" class="form-control" value="<?= $fund['title'] ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Price</label>
              <input type="number" step="0.01" name="price" class="form-control" value="<?= $fund['price'] ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" name="date" class="form-control" value="<?= $fund['date'] ?>" required>
            </div>
            <button type="submit" name="update" class="btn btn-success">Update</button>
            <a href="funds.php" class="btn btn-secondary">Cancel</a>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<?php require "footer.php"; ?>
