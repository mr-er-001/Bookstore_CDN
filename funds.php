<?php 
include "topheader.php"; 
include "dbb.php";

// 🟢 Handle Update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $title = (int)$_POST['title']; // now storing ID of fund_titles
    $price = trim($_POST['price']);
    $date = trim($_POST['date']);

    if ($id > 0 && $title > 0 && $price !== '') {
        $sql = "UPDATE funds SET title = ?, price = ?, date = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("idsi", $title, $price, $date, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Fund updated successfully!');</script>";
            echo "<script>window.open('funds.php','_self');</script>";
            exit;
        } else {
            echo "<script>alert('Error updating fund.');</script>";
        }
    } else {
        echo "<script>alert('Invalid input.');</script>";
    }
}

// fetch all funds with fund names
$result = mysqli_query($conn, "
    SELECT f.id, f.price, f.date, ft.title AS fund_name
    FROM funds f
    LEFT JOIN fund_titles ft ON f.title = ft.id
    ORDER BY f.date DESC
");

// fetch fund titles for dropdown
$fund_titles_result = mysqli_query($conn, "SELECT id, title FROM fund_titles ORDER BY title ASC");
$fund_titles = [];
while($row = mysqli_fetch_assoc($fund_titles_result)) {
    $fund_titles[$row['id']] = $row['title'];
}
?>

<div class="pagetitle">
  <h1>Funds</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.php">Home</a></li>
      <li class="breadcrumb-item">Tables</li>
      <li class="breadcrumb-item active">Funds Data</li>
    </ol>
  </nav>
</div>

<div class="d-flex justify-content-end mt-2 me-2 mb-2">
  <a href="add_fund.php" class="btn btn-sm" style="background-color: #045E70; color: white;">
    <i class="bi bi-plus-circle"></i> New Fund
  </a>
</div>

<section class="section" style="padding:15px;">
  <div class="table-responsive">
    <table class="table table-hover align-middle datatable"
           style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6;">
      <thead>
        <tr>
          <th style="background-color: #045E70; color: white;">Sr. No.</th>
          <th style="background-color: #045E70; color: white;">Title</th>
          <th style="background-color: #045E70; color: white;">Price</th>
          <th style="background-color: #045E70; color: white;">Date</th>
          <th style="background-color: #045E70; color: white;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        $sr = 1;
        if (mysqli_num_rows($result) > 0) {
            while($row = mysqli_fetch_assoc($result)) { 
                echo "<tr>
                        <td>{$sr}</td>
                        <td>".htmlspecialchars($row['fund_name'])."</td>
                        <td>".number_format($row['price'], 2)."</td>
                        <td>".htmlspecialchars($row['date'])."</td>
                        <td>
                          <button class='btn btn-sm btn-success me-1 edit-btn'
                            data-id='{$row['id']}'
                            data-title='{$row['fund_name']}'
                            data-title-id='{$row['title']}' 
                            data-price='".htmlspecialchars($row['price'])."'
                            data-date='".htmlspecialchars($row['date'])."'>
                            <img src='assets/img/elements.png' alt='Edit' style='width:16px;height:16px;'/>
                          </button>
                          <a href='delete_fund.php?id={$row['id']}' class='btn btn-sm btn-danger'
                             onclick='return confirm(\"Are you sure you want to delete this fund?\")'>
                            <img src='assets/img/🦆 icon _trash_.png' alt='Delete' style='width:16px;height:16px;'/>
                          </a>
                        </td>
                      </tr>";
                $sr++;
            }
        } else {
            echo "<tr><td colspan='5' class='text-center text-muted'>No funds found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</section>

<!-- Edit Fund Modal -->
<div class="modal fade" id="editFundModal" tabindex="-1" aria-labelledby="editFundLabel" aria-hidden="true">
  <div class="modal-dialog modal-md">
    <div class="modal-content">
      <form id="editFundForm" method="POST" action="">
        <div class="modal-header" style="background-color:#045E70;color:white;">
          <h5 class="modal-title" id="editFundLabel">Edit Fund</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <input type="hidden" name="id" id="fund_id">

          <div class="mb-3">
            <label class="form-label">Title</label>
            <select class="form-select" name="title" id="fund_title" required>
                <option value="" disabled>Select Fund</option>
                <?php
                foreach($fund_titles as $id => $name) {
                    echo "<option value='{$id}'>{$name}</option>";
                }
                ?>
            </select>
          </div>

          <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" step="0.01" class="form-control" name="price" id="fund_price" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Date</label>
            <input type="date" class="form-control" name="date" id="fund_date" required>
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
  // Open modal and fill fund data
  $(document).on("click", ".edit-btn", function() {
    $("#fund_id").val($(this).data("id"));
    $("#fund_price").val($(this).data("price"));
    $("#fund_date").val($(this).data("date"));

    // set dropdown value by ID
    $("#fund_title").val($(this).data("title-id"));

    const modalEl = document.getElementById('editFundModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
});
</script>

<?php require "footer.php"; ?>
