<?php include "topheader.php"; ?>
<?php include "dbb.php"; ?>

<?php
// 🟢 Handle Update (when Edit Modal is submitted)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)$_POST['id'];
    $company_name = trim($_POST['company_name']);
    $contact_name = trim($_POST['username']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $mobile = trim($_POST['mobile']);
    $address = trim($_POST['address']);

    if ($id > 0 && $company_name !== '') {
        $sql = "UPDATE vendor 
                SET company_name = ?, 
                    contact_name = ?, 
                    email = ?, 
                    phone = ?, 
                    mobile = ?, 
                    postal_address = ?
                WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $company_name, $contact_name, $email, $phone, $mobile, $address, $id);

        if ($stmt->execute()) {
            echo "<script>alert('Vendor updated successfully!');</script>";
            echo "<script>window.open('vendor_data.php','_self');</script>";
            exit;
        } else {
            echo "<script>alert('❌ Error updating vendor.');</script>";
        }
    } else {
        echo "<script>alert('⚠️ Invalid input.');</script>";
    }
}
?>

<section class="section" style="background-color:#F0FDFF; padding:15px;">
  <div class="pagetitle">
    <h1 class="fw-bold">Vendor</h1>
    <nav>
      <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="index.html">Home</a></li>
        <li class="breadcrumb-item">Tables</li>
        <li class="breadcrumb-item active">Vendor Data</li>
      </ol>
    </nav>
  </div>

  <!-- Button aligned right -->
  <div class="d-flex justify-content-end mt-2 me-2" style="margin-bottom: 10px;">
    <a href="vendor_reg.php" class="btn btn-md" style="background-color: #045E70; color: white;">
      New Vendor
    </a>
  </div>

  <div class="table-responsive">
    <table class="table table-hover align-middle datatable"
           style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6;">
      <thead>
        <tr>
          <th scope="col" style="background-color: #045E70; color: white; width: 5%;">#</th>
          <th scope="col" style="background-color: #045E70; color: white;">Vendor Name</th>
          <th scope="col" style="background-color: #045E70; color: white;">Contact Name</th>
          <th scope="col" style="background-color: #045E70; color: white;">Email</th>
          <th scope="col" style="background-color: #045E70; color: white;">Phone</th>
          <th scope="col" style="background-color: #045E70; color: white;">Mobile</th>
          <th scope="col" style="background-color: #045E70; color: white;">Address</th>
          <th scope="col" style="background-color: #045E70; color: white; width: 10%;">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT id, company_name, contact_name, email, phone, mobile, postal_address FROM vendor";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          $sr = 1;
          while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$sr}</td>
                    <td>" . htmlspecialchars($row['company_name']) . "</td>
                    <td>" . htmlspecialchars($row['contact_name']) . "</td>
                    <td>" . htmlspecialchars($row['email']) . "</td>
                    <td>" . htmlspecialchars($row['phone']) . "</td>
                    <td>" . htmlspecialchars($row['mobile']) . "</td>
                    <td>" . htmlspecialchars($row['postal_address']) . "</td>
                    <td>
                      <button class='btn btn-sm btn-success me-1 edit-btn' 
                              data-id='{$row['id']}'
                              data-name='" . htmlspecialchars($row['company_name']) . "'
                              data-username='" . htmlspecialchars($row['contact_name']) . "'
                              data-email='" . htmlspecialchars($row['email']) . "'
                              data-phone='" . htmlspecialchars($row['phone']) . "'
                              data-mobile='" . htmlspecialchars($row['mobile']) . "'
                              data-address='" . htmlspecialchars($row['postal_address']) . "'>
                        <img src='assets/img/elements.png' alt='Edit' style='width:16px;height:16px;'>
                      </button>
                      <a href='vendor_delete.php?id={$row['id']}' 
                         class='btn btn-sm btn-danger'
                         onclick='return confirm(\"Are you sure you want to delete this vendor?\")'>
                        <img src='assets/img/🦆 icon _trash_.png' alt='Delete' style='width:16px;height:16px;'>
                      </a>
                    </td>
                  </tr>";
            $sr++;
          }
        } else {
          echo "<tr><td colspan='8' class='text-center text-muted'>No vendors found</td></tr>";
        }
        ?>
      </tbody>
    </table>
  </div>
</section>

</main>

<!-- ✅ Edit Modal -->
<div class="modal fade" id="editVendorModal" tabindex="-1" aria-labelledby="editVendorLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <form id="editVendorForm" method="POST" action="">
        <div class="modal-header" style="background-color:#045E70;color:white;">
          <h5 class="modal-title" id="editVendorLabel">Edit Vendor</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body row g-3">
          <input type="hidden" name="id" id="vendor_id">

          <div class="col-12">
            <label class="form-label">Vendor Name</label>
            <input type="text" class="form-control" name="company_name" id="company_name"
                   pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" required>
          </div>

          <div class="col-12">
  <label class="form-label">Contact Name</label>
  <input type="text" class="form-control" name="username" id="username"
         pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" required
         oninput="validateContactName(this)">
</div>

<script>
function validateContactName(input) {
  const regex = /^[A-Za-z\s]*$/;
  if (!regex.test(input.value)) {
    alert("Only letters and spaces are allowed in Contact Name.");
    input.value = input.value.replace(/[^A-Za-z\s]/g, ''); // Remove invalid chars
  }
}
</script>

<div class="col-12">
  <label class="form-label">Email</label>
  <input type="email" class="form-control" name="email" id="email"
         pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
         title="Please enter a valid email address (e.g. user@example.com)" required>
</div>


          <div class="col-12">
            <label class="form-label">Phone</label>
            <input type="text" class="form-control" name="phone" id="phone"
                   maxlength="11" pattern="^[0-9]{7,11}$"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" required>
          </div>

          <div class="col-12">
            <label class="form-label">Mobile</label>
            <input type="text" class="form-control" name="mobile" id="mobile"
                   maxlength="11" pattern="^[0-9]{11}$"
                   oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" required>
          </div>

          <div class="col-12">
            <label class="form-label">Postal Address</label>
            <input type="text" class="form-control" name="address" id="address" maxlength="100" required>
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

<script>
$(document).ready(function() {
  // 🟦 Open modal and fill data
  $(document).on("click", ".edit-btn", function() {
    $("#editVendorForm")[0].reset();

    $("#vendor_id").val($(this).data("id"));
    $("#company_name").val($(this).data("name"));
    $("#username").val($(this).data("username"));
    $("#email").val($(this).data("email"));
    $("#phone").val($(this).data("phone"));
    $("#mobile").val($(this).data("mobile"));
    $("#address").val($(this).data("address"));

    const modalEl = document.getElementById('editVendorModal');
    let modal = bootstrap.Modal.getInstance(modalEl);
    if (!modal) modal = new bootstrap.Modal(modalEl);
    modal.show();
  });
});
</script>

<?php require "footer.php"; ?>
