<?php require "topheader.php"; ?>
<?php include 'dbb.php'; ?>

<?php
// 🟢 Handle form submit (Update Publisher)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $publisher_name = trim($_POST['publisher_name']);

    if ($id > 0 && $publisher_name !== '') {
        $sql = "UPDATE publisher SET publisher_name = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $publisher_name, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Publisher updated successfully!');</script>";
            echo "<script>window.open('pub_data.php','_self');</script>";
            exit;
        } else {
            echo "<script>alert('Error updating publisher.');</script>";
        }
    } else {
        echo "<script>alert('Invalid input.');</script>";
    }
}
?>

<section class="section" style="background-color:#F0FDFF; padding:15px;">
  <div class="pagetitle">
    <h1 class="fw-bold">Publisher</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Publisher Data</li>
      </ol>
    </nav>
  </div>

  <!-- Button aligned right -->
  <div class="d-flex justify-content-end mt-2 me-2" style="margin-bottom: 10px;">
    <a href="pub_reg.php" class="btn btn-md" style="background-color: #045E70; color: white;">New Publisher</a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle datatable"
           style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6;">
      <thead>
        <tr>
          <th scope="col" style="background-color: #045E70; color: white; width: 10%;">Sr. No.</th>
          <th scope="col" style="background-color: #045E70; color: white; width: 65%;">Publisher Name</th>
          <th scope="col" style="background-color: #045E70; color: white; width: 25%;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT id, publisher_name FROM publisher";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $sr = 1;
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$sr}</td>
                    <td id='publisher_name_{$row['id']}'>" . htmlspecialchars($row['publisher_name']) . "</td>
                    <td>
                      <button class='btn btn-sm btn-success me-1 edit-btn' 
                              data-id='{$row['id']}' 
                              data-name='" . htmlspecialchars($row['publisher_name']) . "'>
                        <img src='assets/img/elements.png' alt='Edit' style='width:16px;height:16px;'>
                      </button>
                      <a href='delete_publisher.php?id={$row['id']}' 
                         class='btn btn-sm btn-danger'
                         onclick='return confirm(\"Are you sure you want to delete this publisher?\")'>
                        <img src='assets/img/🦆 icon _trash_.png' alt='Delete' style='width:16px;height:16px;'>
                      </a>
                    </td>
                  </tr>";
            $sr++;
          }
        } else {
          echo "<tr><td colspan='3' class='text-center text-muted'>No publishers found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</section>

</main>

<!-- ✅ Edit Modal -->
<div class="modal fade" id="editPublisherModal" tabindex="-1" aria-labelledby="editPublisherLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editPublisherForm" method="POST" action="">
        <div class="modal-header" style="background-color:#045E70;color:white;">
          <h5 class="modal-title" id="editPublisherLabel">Edit Publisher</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="publisher_id">
          <div class="mb-3">
            <label class="form-label">Publisher Name</label>
            <input type="text" class="form-control" name="publisher_name" id="publisher_name" required>
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
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->
<script>
$(document).ready(function() {
  // 🟦 Open modal and fill data
  $(document).on("click", ".edit-btn", function() {
    const id = $(this).data("id");
    const name = $(this).data("name");

    $("#editPublisherForm")[0].reset(); // clear old data
    $("#publisher_id").val(id);
    $("#publisher_name").val(name);

    const modalEl = document.getElementById('editPublisherModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
});
</script>

<!-- ✅ JS Section -->
<?php require "footer.php"; ?>

