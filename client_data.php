<?php include "topheader.php"; ?>
<?php include "dbb.php"; ?>

<?php
// 🟢 Handle Update (when Edit Modal is submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $company_name = trim($_POST['company_name']);
    $contact_name = trim($_POST['contact_name']);
    $postal_address = trim($_POST['postal_address']);
    $phone = trim($_POST['phone']);
    $mobile = trim($_POST['mobile']);
    $email = trim($_POST['email']);

    if ($id > 0 && $company_name !== '') {
        $sql = "UPDATE client 
                SET company_name = ?, contact_name = ?, postal_address = ?, phone = ?, mobile = ?, email = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $company_name, $contact_name, $postal_address, $phone, $mobile, $email, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Client updated successfully!');</script>";
            echo "<script>window.open('client_data.php','_self');</script>";
            exit;
        } else {
            echo "<script>alert('Error updating client.');</script>";
        }
    } else {
        echo "<script>alert('Invalid input.');</script>";
    }
}
?>

<section class="section" style="background-color:#F0FDFF; padding:15px;">
  <div class="pagetitle">
    <h1 class="fw-bold">Clients</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.php">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Client Data</li>
      </ol>
    </nav>
  </div>

  <!-- Button aligned right -->
  <div class="d-flex justify-content-end mt-2 me-2" style="margin-bottom: 10px;">
    <a href="client_reg.php" class="btn btn-md" style="background-color: #045E70; color: white;">
      New Client
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle datatable client-table"
           style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6;">
      <thead>
        <tr>
          <th style="background-color: #045E70; color: white;">#</th>
          <th style="background-color: #045E70; color: white;">Company Name</th>
          <th style="background-color: #045E70; color: white;">Contact Name</th>
          <th style="background-color: #045E70; color: white;">Address</th>
          <th style="background-color: #045E70; color: white;">Phone</th>
          <th style="background-color: #045E70; color: white;">Mobile</th>
          <th style="background-color: #045E70; color: white;">Email</th>
          <th style="background-color: #045E70; color: white;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT id, company_name, contact_name, postal_address, phone, mobile, email FROM client";
        $result = $conn->query($sql);
        if ($result->num_rows > 0) {
          $sr = 1;
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$sr}</td>
                    <td>" . htmlspecialchars($row['company_name']) . "</td>
                    <td>" . htmlspecialchars($row['contact_name']) . "</td>
                    <td>" . htmlspecialchars($row['postal_address']) . "</td>
                    <td>" . htmlspecialchars($row['phone']) . "</td>
                    <td>" . htmlspecialchars($row['mobile']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>
                      <button class='btn btn-sm btn-success me-1 edit-btn'
                        data-id='{$row['id']}'
                        data-company='" . htmlspecialchars($row['company_name']) . "'
                        data-contact='" . htmlspecialchars($row['contact_name']) . "'
                        data-address='" . htmlspecialchars($row['postal_address']) . "'
                        data-phone='" . htmlspecialchars($row['phone']) . "'
                        data-mobile='" . htmlspecialchars($row['mobile']) . "'
                        data-email='" . htmlspecialchars($row['email']) . "'>
                        <img src='assets/img/elements.png' alt='Edit' style='width:16px;height:16px;'>
                      </button>
                      <a href='client_delete.php?id={$row['id']}' class='btn btn-sm btn-danger'
                         onclick='return confirm(\"Are you sure you want to delete this client?\")'>
                        <img src='assets/img/🦆 icon _trash_.png' alt='Delete' style='width:16px;height:16px;'>
                      </a>
                    </td>
                  </tr>";
            $sr++;
          }
        } else {
          echo "<tr><td colspan='8' class='text-center text-muted'>No clients found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</section>

</main>

<!-- ✅ Edit Modal -->
<div class="modal fade" id="editClientModal" tabindex="-1" aria-labelledby="editClientLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editClientForm" method="POST" action="">
        <div class="modal-header" style="background-color:#045E70;color:white;">
          <h5 class="modal-title" id="editClientLabel">Edit Client</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="id" id="client_id">

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">Company Name</label>
              <input type="text" class="form-control" name="company_name" id="company_name" required>
            </div>
            <div class="col-md-6">
              <label class="form-label">Contact Name</label>
              <input type="text" class="form-control" name="contact_name" id="contact_name">
            </div>
            <div class="col-md-12">
              <label class="form-label">Address</label>
              <input type="text" class="form-control" name="postal_address" id="postal_address">
            </div>
            <div class="col-md-6">
              <label class="form-label">Phone</label>
              <input type="number" class="form-control" name="phone" id="phone">
            </div>
            <div class="col-md-6">
              <label class="form-label">Mobile</label>
              <input type="number" class="form-control" name="mobile" id="mobile">
            </div>
            <div class="col-md-12">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" id="email">
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

<!-- ✅ JavaScript -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script> -->

<script>
$(document).ready(function() {
  // 🟦 Open modal and fill client data
  $(document).on("click", ".edit-btn", function() {
    const id = $(this).data("id");
    $("#client_id").val(id);
    $("#company_name").val($(this).data("company"));
    $("#contact_name").val($(this).data("contact"));
    $("#postal_address").val($(this).data("address"));
    $("#phone").val($(this).data("phone"));
    $("#mobile").val($(this).data("mobile"));
    $("#email").val($(this).data("email"));

    const modalEl = document.getElementById('editClientModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
});
</script>

<?php require "footer.php"; ?>
