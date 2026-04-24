<?php require 'topheader.php'; ?>

<div class="container">
  <section class="section register  d-flex flex-column align-items-center justify-content-center py-4 mt-5">
    <div class="row justify-content-center w-100">
      
      <div class="col-lg-9 col-md-8 col-sm-10 col-12">

       

        <div class="card w-100 shadow-sm mb-3">
          <div class="card-body">

            <div class="pt-4 pb-2 text-center">
              <h5 class="card-title fs-4">Register Category</h5>
              <!-- <p class="small">Enter category details to create account</p> -->
            </div>

            <!-- Publisher Form -->
            <form class="row g-3 needs-validation" action="save_cate.php" method="POST" novalidate>
              
              <!-- Publisher Name -->
              <div class="col-12">
  <label for="categoryName" class="form-label">Category Name</label>
  <input type="text" name="category_name" class="form-control" id="categoryName" required>
  <div class="invalid-feedback">Please, enter category name!</div>
</div>


              <!-- Terms -->
              

              <!-- Submit -->
               <div class="row" style="margin-top: 20px;align-items: center; justify-content: center;">
                <div class="col-3">
                <button class="btn btn-primary w-100" type="submit">Create Category</button>
              </div>

              <!-- Back Button -->
              <div class="col-3">
                <a href="cate_data.php" class="btn btn-secondary w-100">Back</a>
              </div>
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

<?php require "footer.php"; ?>

