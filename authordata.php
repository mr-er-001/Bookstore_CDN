<?php require "topheader.php"; ?>

<?php
include 'dbb.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $author_name = trim($_POST['author_name']);

    if ($id > 0 && $author_name !== '') {
        $sql = "UPDATE author SET author_name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $author_name, $id);
        if ($stmt->execute()) {
            
            echo "Author updated successfully!";
          
           echo "<script>window.open('./authordata.php?id=$first_insert_id','_self')</script>";
    exit;
        } else {
            echo "Error updating author.";
        }
    } else {
        echo "Invalid input.";
    }
}
?>

<section class="section" style="background-color:#F0FDFF; padding:15px;">

<div class="pagetitle">
  <h1 class="fw-bold">Author</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">Home</a></li>
      <li class="breadcrumb-item">Tables</li>
      <li class="breadcrumb-item active">Author</li>
    </ol>
  </nav>
</div>

<!-- Button aligned right -->
<div class="d-flex justify-content-end mt-2 me-2" style="margin-bottom: 10px;">
 <a href="register.php" class="btn btn-md" style="background-color: #045E70; color: white;"> New Author </a>
</div>

<table class="table table-hover align-middle datatable"
       style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6;">
  <thead>
    <tr>
      <th scope="col" style="background-color: #045E70; color: white; width: 10%;">Sr. No.</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 65%;">Author Name</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 25%;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    include 'dbb.php';
    $sql = "SELECT id, author_name FROM author";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
      $sr = 1;
      while ($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>{$sr}</td>
                <td id='author_name_{$row['id']}'>" . htmlspecialchars($row['author_name']) . "</td>
                <td>
                  <button class='btn btn-sm btn-success me-1 edit-btn' 
                          data-id='{$row['id']}' data-name='" . htmlspecialchars($row['author_name']) . "'>
                    <img src='assets/img/elements.png' alt='Edit'>
                  </button>
                  <a href='delete_author.php?id={$row['id']}' class='btn btn-sm btn-danger'
                     onclick='return confirm(\"Are you sure you want to delete this author?\")'>
                    <img src='assets/img/🦆 icon _trash_.png' alt='Delete'>
                  </a>
                </td>
              </tr>";
        $sr++;
      }
    } else {
      echo "<tr><td colspan='3' class='text-center text-muted'>No authors found</td></tr>";
    }
    ?>
  </tbody>
</table>

</section>

</main>

<!-- ✅ Bootstrap Modal -->
<div class="modal fade" id="editAuthorModal" tabindex="-1" aria-labelledby="editAuthorLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editAuthorForm" method="POST" action="">
        <div class="modal-header" style="background-color:#045E70;color:white;">
          <h5 class="modal-title" id="editAuthorLabel">Edit Author</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="author_id">
          <div class="mb-3">
            <label class="form-label">Author Name</label>
            <input type="text" class="form-control" name="author_name" id="author_name" required>
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

<?php require "footer.php"; ?>



