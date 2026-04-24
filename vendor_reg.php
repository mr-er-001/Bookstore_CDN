<?php require 'topheader.php'; ?>


    <div class="container">
  <section class="section register  d-flex flex-column align-items-center justify-content-center py-4 ">
    <div class="container">
      <div class="row justify-content-center">
        
        <!-- Make form width responsive -->
        <div class="col-lg-9 col-md-8 col-sm-10 col-12 d-flex flex-column align-items-center justify-content-center">



          <div class="card w-100 shadow-sm mb-3">
            <div class="card-body">

              <div class="pt-4 pb-2 text-center">
                <h5 class="card-title fs-4">Register Vendor</h5>
                <!-- <p class="small">Enter your personal details to create account</p> -->
              </div>  

              <!-- Responsive form -->
               <form class="row g-3 needs-validation" action="save_vendor.php" method="POST" novalidate>
            
           <div class="col-12">
  <label for="vendorName" class="form-label">Vendor Name</label>
  <input type="text" name="company_name" class="form-control" id="vendorName"
         pattern="[A-Za-z\s]+" title="Only letters and spaces allowed" required>
  <div class="invalid-feedback">Please enter vendor name (letters only)!</div>
</div>


  <!-- Contact Name -->
 <div class="col-12">
  <label for="yourUsername" class="form-label">Contact Name</label>
  <div class="input-group has-validation">
    <span class="input-group-text">@</span>
    <input 
      type="text" 
      name="username" 
      class="form-control" 
      id="yourUsername" 
      required 
      pattern="^[A-Za-z\s]+$" 
      title="Only letters and spaces are allowed"
    >
    <div class="invalid-feedback">
      Please enter a valid contact name (letters only).
    </div>
  </div>
</div>

  <!-- Email -->

  <div class="col-12">
    <label for="yourEmail" class="form-label">Email</label>
    <input 
      type="email" 
      name="email" 
      class="form-control" 
      id="yourEmail" 
      placeholder="example@gmail.com" 
      required>
    <div class="invalid-feedback">Please enter a valid email address!</div>
  </div>


<!-- Phone -->
<div class="col-12">
  <label for="phone" class="form-label">Phone</label>
  <input type="text" name="phone" class="form-control" id="phone"
         maxlength="11" pattern="^[0-9]{7,11}$" required
         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
  <div class="invalid-feedback">Please enter a valid phone number (7–11 digits)!</div>
</div>

<!-- Mobile -->
<div class="col-12">
  <label for="mobile" class="form-label">Mobile No</label>
  <input type="text" name="mobile" class="form-control" id="mobile"
         maxlength="11" pattern="^[0-9]{11}$" required
         oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 11)">
  <div class="invalid-feedback">Please enter a valid 11-digit mobile number!</div>
</div>

<!-- Postal Address -->
<div class="col-12">
  <label for="address" class="form-label">Postal Address</label>
  <input type="text" name="address" class="form-control" id="address" maxlength="500" required>
  <div class="invalid-feedback">Please enter your postal address (max 500 characters)!</div>
</div>




            <!-- Terms -->
     

            <!-- Submit -->
            <div class="row" style="margin-top: 20px;align-items: center; justify-content: center;">
                <div class="col-3">
                <button class="btn btn-primary w-100" type="submit">Create Vendor</button>
              </div>

              <!-- Back Button -->
              <div class="col-3">
                <a href="vendor_data.php" class="btn btn-secondary w-100">Back</a>
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


<script>
document.getElementById("vendorName").addEventListener("input", function() {
  this.value = this.value.replace(/[^A-Za-z\s]/g, ''); // Remove non-alphabet characters
});
</script>
<script>
document.getElementById("yourUsername").addEventListener("input", function() {
  // Remove any characters that are not letters or spaces
  this.value = this.value.replace(/[^A-Za-z\s]/g, '');
});
</script>
<!-- <script>
document.getElementById("yourEmail").addEventListener("blur", function() {
  const email = this.value.trim();
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // Basic email format

  if (email !== "" && !emailPattern.test(email)) {
    alert("❌ Please enter a valid email address (e.g. example@gmail.com)");
    this.focus();
  }
});
</script> -->
<script>
(function () {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>




 <?php require "footer.php"; ?>

 