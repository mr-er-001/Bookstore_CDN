<?php
include 'dbb.php';
include 'topheader.php';
?>

<section class="section" style="background-color:#F0FDFF; padding:15px;">

<div class="pagetitle">
  <h1 class="fw-bold">User Management</h1>
  <nav>
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="index.html">Home</a></li>
      <li class="breadcrumb-item">Tables</li>
      <li class="breadcrumb-item active">Users</li>
    </ol>
  </nav>
</div>

<!-- Button aligned right -->
<div class="d-flex justify-content-end mt-2 me-2" style="margin-bottom: 10px;">
  <a href="user_form.php" class="btn btn-md" style="background-color: #045E70; color: white;"> 
    <i class="bi bi-person-plus-fill me-1"></i> New User 
  </a>
</div>

<table class="table table-hover align-middle datatable"
       style="table-layout: fixed; width: 100%; background-color: white; border:1px solid #dee2e6;">
  <thead>
    <tr>
      <th scope="col" style="background-color: #045E70; color: white; width: 10%;">Staff ID</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 15%;">Account Name</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 15%;">Username</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 20%;">Email</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 15%;">Mobile</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 10%;">Password</th>
      <th scope="col" style="background-color: #045E70; color: white; width: 15%;">Actions</th>
    </tr>
  </thead>
  <tbody>
    <?php
    $query = "SELECT * FROM admin";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "
            <tr>
                <td>{$row['staff_id']}</td>
                <td>" . htmlspecialchars($row['account_name']) . "</td>
                <td>" . htmlspecialchars($row['username']) . "</td>
                <td>" . htmlspecialchars($row['email']) . "</td>
                <td>" . htmlspecialchars($row['mobile']) . "</td>
                <td>" . htmlspecialchars($row['password']) . "</td>
                <td>
                  <button class='btn btn-sm btn-success me-1 edit-btn'
                      data-id='{$row['staff_id']}' 
                      data-name='" . htmlspecialchars($row['account_name']) . "' 
                      data-username='" . htmlspecialchars($row['username']) . "' 
                      data-email='" . htmlspecialchars($row['email']) . "' 
                      data-mobile='" . htmlspecialchars($row['mobile']) . "' 
                      data-password='" . htmlspecialchars($row['password']) . "'>
                      <img src='assets/img/elements.png' alt='Edit'>
                  </button>
                  <a href='delete_user.php?id={$row['staff_id']}'
   class='btn btn-sm btn-danger delete-btn'
   data-id='{$row['staff_id']}'
   data-name='" . htmlspecialchars($row['account_name']) . "'
   onclick='return confirm(\"Are you sure you want to delete this user?\")'>
   <img src='assets/img/🦆 icon _trash_.png' alt='Delete'>
</a>

                </td>
            </tr>";
        }
    } else {
        echo "<tr><td colspan='7' class='text-center text-muted'>No users found</td></tr>";
    }
    ?>
  </tbody>
</table>

</section>
</main>

<!-- ✅ Edit Modal -->
<div class="modal fade" id="editUserModal" tabindex="-1" aria-labelledby="editUserLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form id="editUserForm" method="POST" action="update_user.php">
        <div class="modal-header" style="background-color:#045E70;color:white;">
          <h5 class="modal-title" id="editUserLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <input type="hidden" name="staff_id" id="edit_id">

          <div class="mb-3">
            <label class="form-label">Account Name</label>
            <input type="text" class="form-control" name="account_name" id="edit_name" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Username</label>
            <input type="text" class="form-control" name="username" id="edit_username" required>
          </div>

          <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" class="form-control" name="email" id="edit_email">
          </div>

          <div class="mb-3">
            <label class="form-label">Mobile</label>
            <input type="number" class="form-control" name="mobile" id="edit_mobile">
          </div>

          <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="text" class="form-control" name="password" id="edit_password" placeholder="Enter new password (leave blank to keep old one)">
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

<!-- //🗑 Delete Modal -->
<!-- <div class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content border-0 shadow-sm">
      <form method="POST" action="delete_user.php">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="deleteUserLabel">Delete User</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body text-center">
          <input type="hidden" name="staff_id" id="delete_id">
          <p class="fs-6">Are you sure you want to delete <strong id="delete_name"></strong>?</p>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="submit" class="btn btn-danger px-4">Yes, Delete</button>
          <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div> -->

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
// 🔹 Open Edit Modal and fill data
$(document).on('click', '.edit-btn', function() {
    $('#edit_id').val($(this).data('id'));
    $('#edit_name').val($(this).data('name'));
    $('#edit_username').val($(this).data('username'));
    $('#edit_email').val($(this).data('email'));
    $('#edit_mobile').val($(this).data('mobile'));
    $('#edit_password').val($(this).data('password'));
    $('#editUserModal').modal('show');
});

// 🔹 Delete modal trigger
$(document).on('click', '.delete-btn', function() {
    $('#delete_id').val($(this).data('id'));
    $('#delete_name').text($(this).data('name'));
    $('#deleteUserModal').modal('show');
});

// ✅ EDIT FORM VALIDATION
$('#editUserForm').on('submit', function(e) {
    const name = $('#edit_name').val().trim();
    const username = $('#edit_username').val().trim();
    const email = $('#edit_email').val().trim();
    const mobile = $('#edit_mobile').val().trim();

    let isValid = true;
    let errorMessage = '';

    if (name === '') {
        isValid = false;
        errorMessage = 'Account Name is required.';
    } else if (username === '') {
        isValid = false;
        errorMessage = 'Username is required.';
    } else if (email !== '' && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        isValid = false;
        errorMessage = 'Please enter a valid email address.';
    } else if (mobile !== '' && !/^(03[0-9]{9})$/.test(mobile)) {
        isValid = false;
        errorMessage = 'Please enter a valid mobile number (e.g., 03001234567).';
    }

    if (!isValid) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Validation Error',
            text: errorMessage,
            confirmButtonColor: '#045E70'
        });
    }
});


</script>
<style>
  /* Hide arrows in Chrome, Safari, Edge, Opera */
input[type=number]::-webkit-inner-spin-button,
input[type=number]::-webkit-outer-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

/* Hide arrows in Firefox */
input[type=number] {
  -moz-appearance: textfield;
}

</style>
<?php include 'footer.php'; ?>
