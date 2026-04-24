<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM category WHERE id=$id");
    $category = mysqli_fetch_assoc($result);
}

if (isset($_POST['update'])) {
    $category_name = mysqli_real_escape_string($conn, $_POST['category_name']);

    $sql = "UPDATE category SET category_name='$category_name' WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: cate_data.php"); // redirect back to category list
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

include "topheader.php";
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <div class="card shadow border-0">
        <div class="card-header  text-white" style="background-color: #045E70;">
          <h5 class="mb-0">Edit Category</h5>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Category Name</label>
              <input type="text" 
                     class="form-control" 
                     name="category_name" 
                     value="<?= htmlspecialchars($category['category_name']) ?>" 
                     required>
            </div>
            <button type="submit" name="update" class="btn  w-100" style="background-color: #045E70;color:white">Update Category</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
@media (min-width: 1200px) {
    #main, #footer {
        margin-top: 150px;
        margin-left: 180px;
    }
}
</style>

<?php include "footer.php"; ?>
