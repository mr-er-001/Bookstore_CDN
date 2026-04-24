<?php require 'topheader.php'; ?>
<style>
body {
    background: #e5f4f9;
    font-family: 'Open Sans', sans-serif;
}
.card-premium {
    border-radius: 15px;
    border: none;
    box-shadow: 0 6px 20px rgba(0,0,0,0.08);
    background: #ffffff;
}
.page-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #045E70;
    margin-bottom: 15px;
}
.form-label {
    font-weight: bold;
    font-size: 0.875rem;
    color: #0890A6;
}
.form-control {
    border-radius: 5px !important;
    border: 1px solid #045E70;
    font-size: 0.9rem;
    transition: border 0.3s, box-shadow 0.3s;
}
.form-control:focus {
    border-color: #045E70;
    box-shadow: 0 0 6px rgba(4,94,112,0.3);
    outline: none;
}
.btn-primary {
    background-color: #045E70;
    color: #ffffff;
    font-weight: 500;
    border-radius: 12px;
    padding: 8px 20px;
}
.btn-primary:hover {
    opacity: 0.9;
}
.btn-secondary {
    background-color: #6c757d;
    color: #fff;
    font-weight: 500;
    border-radius: 12px;
    padding: 8px 20px;
}
.btn-secondary:hover {
    opacity: 0.9;
}
</style>
<div class="container">
  <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
    <div class="row justify-content-center w-100">
      
      <div class="col-lg-12 col-md-8 col-sm-10 col-12">

     

        <div class="card w-100 shadow-sm mb-3">
          <div class="card-body">

            <div class="pt-4 pb-2 text-center">
              <h5 class="card-title fs-4">Register Client</h5>
        
            </div>

            <!-- Client Form -->
            <form class="row g-3 needs-validation" action="save_client.php" method="POST" novalidate>
  <div class="row" style="margin-top: 10px;">
     <div class="col-4">
    <label for="companyName" class="form-label">Company Name</label>
    <input 
      type="text" 
      name="company_name" 
      class="form-control" 
      id="companyName" 
      required 
      pattern="^[A-Za-z\s]+$" 
      title="Only letters and spaces allowed"
      placeholder="Enter company name">
    <div class="invalid-feedback">Please enter a valid company name (letters only).</div>
  </div>

  <div class="col-4">
    <label for="contactName" class="form-label">Contact Name</label>
    <input 
      type="text" 
      name="contact_name" 
      class="form-control" 
      id="contactName" 
      required 
      pattern="^[A-Za-z\s]+$" 
      title="Only letters and spaces allowed"
      placeholder="Enter contact name">
    <div class="invalid-feedback">Please enter a valid contact name (letters only).</div>
  </div>

  <div class="col-4">
    <label for="email" class="form-label">Email</label>
    <input 
      type="email" 
      name="email" 
      class="form-control" 
      id="email" 
      required 
      placeholder="example@gmail.com">
    <div class="invalid-feedback">Please enter a valid email address!</div>
  </div>
  </div>

  <div class="row" style="margin-top: 10px;">
  <div class="col-4">
    <label for="phone" class="form-label">Phone</label>
    <input type="text" 
           name="phone" 
           class="form-control" 
           id="phone"
           maxlength="11"
           pattern="^[0-9]{7,11}$"
           title="Phone number must be between 7 and 11 digits"
           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" 
           required>
  </div>

  <div class="col-4">
    <label for="mobile" class="form-label">Mobile No</label>
    <input type="text" 
           name="mobile" 
           class="form-control" 
           id="mobile"
           maxlength="11"
           pattern="^[0-9]{11}$"
           title="Mobile number must be exactly 11 digits"
           oninput="this.value=this.value.replace(/[^0-9]/g,'').slice(0,11)" 
           required>
  </div>

  <div class="col-4">
    <label for="address" class="form-label">Postal Address</label>
    <input type="text" 
           name="postal_address" 
           class="form-control" 
           id="address"
           maxlength="10"
           required>
  </div>
</div>




              <div class="row" style="margin-top: 10px;">
              <div class="col-4">
                <label for="clientName" class="form-label">Khalid Book Depot</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Khalid Publishers</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Azhar Publisher</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              </div>



              <div class="row" style="margin-top: 10px;">
              <div class="col-4">
                <label for="clientName" class="form-label">Trand Book</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Sunshine Publication</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Ferozsons</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              </div>


              <div class="row" style="margin-top: 10px;">
              <div class="col-4">
                <label for="clientName" class="form-label">Apolo</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Qomi Kutab Khana</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Salman Publishers Urdu Bazar</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              </div>

              <div class="row" style="margin-top: 10px;">
              <div class="col-4">
                <label for="clientName" class="form-label">Tips Academy</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Anmol</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Azhar Pub (Paper)</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              </div>


              <div class="row" style="margin-top: 10px;">
              <div class="col-4">
                <label for="clientName" class="form-label">Linkman</label> 
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Yawar Pub</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              <div class="col-4">
                <label for="clientName" class="form-label">Tehzib Sona</label>
                <input type="text" name="client_name" class="form-control" id="clientName" value="0" required>
                <div class="invalid-feedback">Please, enter the client name!</div>
              </div>
              </div>
              <!-- Terms -->
            
              <!-- Submit -->

            <div class="row" style="margin-top: 20px;align-items: center; justify-content: center;">
    <div class="col-3">
      <button class="btn btn-primary w-100" type="submit">Create Client</button>
    </div>
    <div class="col-3">
      <a href="client_data.php" class="btn btn-secondary w-100">Back</a>
    </div>
  </div>
</form>

          </div>
        </div>

      </div>
    </div>
  </section>
</div>

<script>
// Bootstrap validation script
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
})();
</script>
<script>
// ✅ Realtime cleanup for names (removes numbers/symbols while typing)
document.getElementById("companyName").addEventListener("input", function() {
  this.value = this.value.replace(/[^A-Za-z\s]/g, '');
});
document.getElementById("contactName").addEventListener("input", function() {
  this.value = this.value.replace(/[^A-Za-z\s]/g, '');
});

// ✅ Bootstrap-style form validation
(() => {
  'use strict';
  const form = document.querySelector('#vendorForm');
  
  form.addEventListener('submit', event => {
    if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      alert("❌ Please fill all fields correctly before submitting.");
    }
    form.classList.add('was-validated');
  }, false);
})();
</script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {

  // 🔹 Check company name on blur
  $("#companyName").on("blur", function() {
    const companyName = $(this).val().trim();
    if (companyName !== "") {
      $.ajax({
        url: "check_client.php",
        type: "POST",
        data: { company_name: companyName },
        dataType: "json",
        success: function(response) {
          if (response.exists && response.field === "company_name") {
            alert("⚠️ This company name already exists. Please use another name.");
            $("#companyName").val("").focus();
          }
        }
      });
    }
  });

  // 🔹 Check email on blur
  $("#email").on("blur", function() {
    const email = $(this).val().trim();
    if (email !== "") {
      $.ajax({
        url: "check_client.php",
        type: "POST",
        data: { email: email },
        dataType: "json",
        success: function(response) {
          if (response.exists && response.field === "email") {
            alert("⚠️ This email is already registered. Please use another one.");
            $("#email").val("").focus();
          }
        }
      });
    }
  });

});
</script>

<?php require "footer.php"; ?>
