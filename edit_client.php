<?php
include "dbb.php";

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $result = mysqli_query($conn, "SELECT * FROM client WHERE id=$id");
    $client = mysqli_fetch_assoc($result);
}

if (isset($_POST['update'])) {
    $company_name   = mysqli_real_escape_string($conn, $_POST['company_name']);
    $contact_name   = mysqli_real_escape_string($conn, $_POST['contact_name']);
    $postal_address = mysqli_real_escape_string($conn, $_POST['postal_address']);
    $phone          = mysqli_real_escape_string($conn, $_POST['phone']);
    $mobile         = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);

    $sql = "UPDATE client 
            SET company_name='$company_name',
                contact_name='$contact_name',
                postal_address='$postal_address',
                phone='$phone',
                mobile='$mobile',
                email='$email'
            WHERE id=$id";

    if (mysqli_query($conn, $sql)) {
        header("Location: client_data.php"); 
        exit;
    } else {
        echo "Error updating record: " . mysqli_error($conn);
    }
}

include "topheader.php";
?>

<div class="container my-5">
  <div class="row justify-content-center">
    <div class="col-md-8">
      <div class="card shadow border-0">
        <div class="card-header  text-white" style="background-color: #045E70;">
          <h5 class="mb-0">Edit Client</h5>
        </div>
        <div class="card-body">
          <form method="POST">
            <div class="mb-3">
              <label class="form-label">Company Name</label>
              <input type="text" class="form-control" name="company_name" 
                     value="<?= htmlspecialchars($client['company_name']) ?>" required>
            </div>
            <div class="mb-3">
              <label class="form-label">Contact Name</label>
              <input type="text" class="form-control" name="contact_name" 
                     value="<?= htmlspecialchars($client['contact_name']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Postal Address</label>
              <input type="text" class="form-control" name="postal_address" 
                     value="<?= htmlspecialchars($client['postal_address']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Phone</label>
              <input type="text" class="form-control" name="phone" 
                     value="<?= htmlspecialchars($client['phone']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Mobile</label>
              <input type="text" class="form-control" name="mobile" 
                     value="<?= htmlspecialchars($client['mobile']) ?>">
            </div>
            <div class="mb-3">
              <label class="form-label">Email</label>
              <input type="email" class="form-control" name="email" 
                     value="<?= htmlspecialchars($client['email']) ?>">
            </div>
            <button type="submit" name="update" class="btn w-100" style="background-color: #045E70;color:white;">Update Client</button>
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
