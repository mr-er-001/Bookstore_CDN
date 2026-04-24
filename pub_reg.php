<?php require 'topheader.php'; ?>


    <div class="container">
  <section class="section register  d-flex flex-column align-items-center justify-content-center py-4 mt-5">
    <div class="container">
      <div class="row justify-content-center">
        
        <!-- Make form width responsive -->
        <div class="col-lg-9 col-md-8 col-sm-10 col-12 d-flex flex-column align-items-center justify-content-center">

   

          <div class="card w-100 shadow-sm mb-3">
            <div class="card-body">

              <div class="pt-4 pb-2 text-center">
                <h5 class="card-title fs-4">Register Publisher</h5>
                <p class="small">Enter your personal details to create account</p>
              </div>

              <!-- Responsive form -->
              <form class="row g-3 needs-validation" action="save_pub.php" method="POST" novalidate>
                
                <!-- Name -->
               <div class="col-12">
  <label for="yourName" class="form-label">Publisher Name</label>
  <input type="text" name="publisher_name" class="form-control" id="yourName" required>
  <div class="invalid-feedback">Please, enter publisher name!</div>
</div>
                <!-- Submit -->
                 <div class="row" style="margin-top: 20px;align-items: center; justify-content: center;">
                <div class="col-3">
                <button class="btn btn-primary w-100" type="submit">Create Publisher</button>
              </div>

              <!-- Back Button -->
              <div class="col-3">
                <a href="pub_data.php" class="btn btn-secondary w-100">Back</a>
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

  
<?php require "footer.php"; ?>