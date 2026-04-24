<?php require 'topheader.php'; ?>
<div class="container">
  <section class="section register d-flex flex-column align-items-center justify-content-center py-4">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-9 col-md-8 col-sm-10 col-12 d-flex flex-column align-items-center justify-content-center">

          <div class="card w-100 shadow-sm mb-3">
            <div class="card-body">
              <div class="pt-4 pb-2 text-center">
                <h5 class="card-title fs-4">Register User</h5>
              </div>  

              <form class="row g-3 needs-validation" action="save_user.php" method="POST" novalidate>
                
                <!-- Full Name -->
                <div class="col-12">
                  <label for="fullName" class="form-label">Full Name</label>
                  <input type="text" name="full_name" class="form-control" id="fullName"
                         pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" required
                         tabindex="1">
                  <div class="invalid-feedback">Please enter your full name (letters only)!</div>
                </div>

                <!-- Username -->
                <div class="col-12">
                  <label for="userName" class="form-label">Username</label>
                  <input type="text" name="username" class="form-control" id="userName"
                         pattern="[A-Za-z0-9_]+" title="Only letters, numbers, and underscores allowed" required
                         tabindex="2">
                  <div class="invalid-feedback">Please enter a valid username!</div>
                </div>

                <!-- Password -->
                <div class="col-12 position-relative">
                  <label for="password" class="form-label">Password</label>
                  <div class="input-group">
                    <input type="password" name="password" class="form-control" id="password"
                           minlength="6" maxlength="20" required placeholder="Enter password"
                           tabindex="3">
                    <button type="button" class="btn btn-outline-secondary" id="togglePassword" tabindex="-1">
                      <i class="bi bi-eye"></i>
                    </button>
                  </div>
                  <div class="invalid-feedback">Password must be at least 6 characters long!</div>
                </div>

                <!-- Email -->
                <div class="col-12">
                  <label for="yourEmail" class="form-label">Email</label>
                  <input type="email" name="email" class="form-control" id="yourEmail"
                         pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,}$"
                         title="Please enter a valid email (e.g. example@gmail.com)"
                         required placeholder="example@gmail.com"
                         tabindex="4">
                  <div class="invalid-feedback">Please enter a valid email address!</div>
                </div>

                <!-- Mobile -->
                <div class="col-12">
                  <label for="mobile" class="form-label">Mobile No</label>
                  <input type="text" name="mobile" class="form-control" id="mobile"
                         maxlength="11" pattern="^[0-9]{11}$"
                         title="Enter exactly 11 digits"
                         required oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)"
                         tabindex="5">
                  <div class="invalid-feedback">Please enter a valid 11-digit mobile number!</div>
                </div>

                <!-- Buttons -->
                <div class="row mt-4 justify-content-center">
                  <div class="col-3">
                    <button class="btn btn-primary w-100" type="submit" tabindex="6">Save User</button>
                  </div>
                  <div class="col-3">
                    <a href="user_for.php" class="btn btn-secondary w-100" tabindex="7">Back</a>
                  </div>
                </div>
              </form>
            </div>
          </div>

        </div>
      </div>
    </div>
  </section>
</div>


<!-- ✅ Enable Bootstrap Validation -->
<script>
(() => {
  'use strict'
  const forms = document.querySelectorAll('.needs-validation')

  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>

<!-- ✅ Input Sanitization -->
<script>
document.getElementById("fullName").addEventListener("input", function() {
  this.value = this.value.replace(/[^A-Za-z\s]/g, '');
});
document.getElementById("userName").addEventListener("input", function() {
  this.value = this.value.replace(/[^A-Za-z0-9_]/g, '');
});
</script>
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<script>
// 👁️ Toggle password visibility
document.getElementById("togglePassword").addEventListener("click", function() {
  const passwordInput = document.getElementById("password");
  const icon = this.querySelector("i");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    icon.classList.remove("bi-eye");
    icon.classList.add("bi-eye-slash");
  } else {
    passwordInput.type = "password";
    icon.classList.remove("bi-eye-slash");
    icon.classList.add("bi-eye");
  }
});
</script>


<?php require "footer.php"; ?>
