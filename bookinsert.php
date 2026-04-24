

<?php include 'topheader.php';


if (isset($_POST['save'])) {
    // Collect form data
    $isbn           = $_POST['isbn'];
    $edition        = $_POST['edition'];
    $pub_id         = $_POST['publisher_id'];
    $pub_date = date('Y-m-d', strtotime(str_replace('/', '-', $_POST['pub_date'])));
    $author_id      = $_POST['author'];
    $pages          = $_POST['pages'];
    $purchase_price = $_POST['purchase_price'];
    $language       = $_POST['language'];
    $quantity       = $_POST['quantity'];
    $title          = $_POST['title'];
    $volume         = $_POST['volume'];
    $pub_short      = $_POST['pub_short'];
    $min_qty        = $_POST['min_qty'];
    $discount       = $_POST['discount'];
    $vendor         = $_POST['vendor'];
    $category       = $_POST['category'];
    $class          = $_POST['class'];
    $sale_price     = $_POST['sale_price'];

    // Check if selected Vendor exists
    $vendorCheck = $conn->query("SELECT id FROM vendor WHERE id = '$vendor'");
    if ($vendorCheck->num_rows == 0) {
        $error = "❌ Selected Vendor does not exist!";
    }
    // Check if selected Category exists
    elseif ($category != '' && $conn->query("SELECT id FROM category WHERE id = '$category'")->num_rows == 0) {
        $error = "❌ Selected Category does not exist!";
    } 
    else {
        // Insert into books table
       // Handle image upload (optional)
$imagePath = '';
if (!empty($_FILES['image']['name'])) {
    $targetDir = "uploads/";
    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0777, true);
    }
    $imagePath = $targetDir . basename($_FILES['image']['name']);
    move_uploaded_file($_FILES['image']['tmp_name'], $imagePath);
}

// ✅ Use the actual $min_qty variable instead of hardcoded '0'
$sql = "INSERT INTO books (
            isbn, title, purchase_price, discount, publisher_id, pub_short, class, vendor_id, 
            published_date, pages, auther_id, sale_price, language, edition, volume, quantity, 
            min_qty, image, category
        ) VALUES (
            '$isbn', '$title', '$purchase_price', '$discount', '$pub_id', '$pub_short', '$class', '$vendor',
            '$pub_date', '$pages', '$author_id', '$sale_price', '$language', '$edition', '$volume', '$quantity',
            '$min_qty', '$imagePath', '$category'
        )";


       if ($conn->query($sql) === TRUE) {
    echo "<script>
            alert('✅ Book Registered Successfully!');
            window.location.href = './tables.php';
          </script>";
    exit;
}

    }
}
?>
<style>
  .result-box {
  position: absolute;
  background: white;
  border: 1px solid #ccc;
  border-radius: 0.25rem;
  max-height: 200px;
  overflow-y: auto;
  z-index: 1000;
}
.author-item:hover {
  background-color: #f8f9fa;
}

</style>




<div class="pagetitle">
      <h1>Add Book</h1>
      <nav>
        <!-- <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Forms</li>
          <li class="breadcrumb-item active">Elements</li>
        </ol> -->
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
  <div class="row justify-content-center">
    <div class="col-lg-12">

      <div class="card shadow-lg border-0 rounded-4">
        <div class="card-body p-4">
         

          <!-- General Form Elements -->
          <form method="post" enctype="multipart/form-data" class="needs-validation">

           <div class="row g-3">
  <div class="col-md-4">
    <label class="form-label fw-semibold">ISBN</label>
    <input type="text" class="form-control" name="isbn" id="isbn" required>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Edition</label>
    <input type="text" class="form-control" name="edition" required>
  </div>
<div class="col-md-4">
    <label class="form-label fw-semibold">Published Date</label>
    <input type="text" class="form-control date-picker" name="pub_date" 
           placeholder="dd-mm-yyyy" maxlength="10" required autocomplete="off">
    <input type="hidden" class="date-mysql" name="pub_date_mysql">
</div>
</div>

<div class="row g-3 mt-1">
  <!-- Author -->
  <div class="col-md-4 position-relative">
    <label class="form-label fw-semibold">Author</label>
    <input type="text" class="form-control search-author" placeholder="Search Author" autocomplete="off" required>
    <input type="hidden" name="author" class="author-id">
    <div class="authorResults result-box w-100"></div>
  </div>

  <!-- Pages -->
  <div class="col-md-4">
    <label class="form-label fw-semibold">Pages</label>
    <input type="number" min="1" class="form-control" name="pages" required>
  </div>

  <!-- Purchase Price -->
  <div class="col-md-4">
    <label class="form-label fw-semibold">Purchase Price</label>
    <input type="number" step="0.01" class="form-control" name="purchase_price" required>
  </div>
</div>


<div class="row g-3 mt-1">
  <div class="col-md-4">
    <label class="form-label fw-semibold">Language</label>
    <input type="text" class="form-control" name="language" required>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Quantity</label>
    <input type="number" min="0" class="form-control" name="quantity" required>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Image</label>
    <input class="form-control" type="file" name="image" accept="image/*" >
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-md-4">
    <label for="title" class="form-label fw-semibold">Title</label>
    <input type="text" id="title" name="title" class="form-control" required>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Volume</label>
    <input type="text" class="form-control" name="volume">
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Publisher Short Name</label>
    <input type="text" class="form-control" name="pub_short" required>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-md-4">
    <label class="form-label fw-semibold">Minimum Qty</label>
    <input type="number" min="0" class="form-control" name="min_qty" required>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Discount (%)</label>
    <input type="number" step="0.01" min="0" max="100" class="form-control" name="discount" required>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Vendor</label>
    <select class="form-select" name="vendor" id="vendor" required>
      <option value="" selected disabled>Select Vendor</option>
      <?php
      $sql = "SELECT id, company_name FROM vendor";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['id']}'>{$row['company_name']}</option>";
          }
      } else {
          echo "<option disabled>No Vendors Found</option>";
      }
      ?>
    </select>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-md-4">
    <label class="form-label fw-semibold">Category</label>
    <select class="form-select" name="category">
      <?php
      $sql = "SELECT id, category_name FROM category";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['id']}'>{$row['category_name']}</option>";
          }
      } else {
          echo "<option disabled>No Categories Found</option>";
      }
      ?>
    </select>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Class</label>
    <input type="text" class="form-control" name="class" required>
  </div>
  <div class="col-md-4">
    <label class="form-label fw-semibold">Sale Price</label>
    <input type="number" step="0.01" class="form-control" name="sale_price" required>
  </div>
</div>

<div class="row g-3 mt-1">
  <div class="col-md-4">
    <label class="form-label fw-semibold">Publisher</label>
    <select class="form-select" name="publisher_id">
      <?php
      $sql = "SELECT id, publisher_name FROM publisher";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo "<option value='{$row['id']}'>{$row['publisher_name']}</option>";
          }
      } else {
          echo "<option disabled>No Publishers Found</option>";
      }
      ?>
    </select>
  </div>
</div>

            <div id="duplicate-warning" class="alert alert-warning mt-3 d-none">
    ⚠️ This book already exists for this vendor!
</div>

            <div class="d-flex justify-content-end mt-4">
              <button type="submit" class="btn  px-4" name="save"  style="background-color: #045E70; color: white;">
                Save Book
              </button>
            </div>

            <?php if (isset($_GET['success'])): ?>
    <div class="alert alert-success">
        ✅ Book Registered Successfully!
    </div>
<?php endif; ?>


          </form>

        </div>
      </div>

    </div>
  </div>
</section>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>

document.addEventListener("DOMContentLoaded", function() {
  const searchBox = document.querySelector(".search-author");
  const resultBox = document.querySelector(".authorResults");
  const hiddenInput = document.querySelector(".author-id");

  // Live author search
  searchBox.addEventListener("keyup", function() {
    const query = this.value.trim();

    if (query.length < 1) { // ✅ search even for single letters
      resultBox.innerHTML = "";
      return;
    }

    fetch("search_author.php?q=" + encodeURIComponent(query))
      .then(response => response.text())
      .then(data => {
        resultBox.innerHTML = data;
      });
  });

  // Handle author selection
  resultBox.addEventListener("click", function(e) {
    if (e.target.classList.contains("author-item")) {
      const authorName = e.target.textContent.trim();
      const authorId = e.target.getAttribute("data-id");

      searchBox.value = authorName;
      hiddenInput.value = authorId;
      resultBox.innerHTML = "";
    }
  });
});

  
function checkDuplicate() {
    let isbn = $("#isbn").val().trim();
    let vendor = $("#vendor").val();
    let title = $("#title").val().trim(); // Get title field

    if (isbn !== "" && vendor !== null && title !== "") {
        $.ajax({
            url: "check_duplicate.php",
            type: "POST",
            data: { isbn: isbn, vendor: vendor, title: title }, // send title too
            success: function (response) {
                if (response === "duplicate") {
                    $("#duplicate-warning").removeClass("d-none");
                    $("button[name='save']").prop("disabled", true);
                } else {
                    $("#duplicate-warning").addClass("d-none");
                    $("button[name='save']").prop("disabled", false);
                }
            }
        });
    } else {
        $("#duplicate-warning").addClass("d-none");
        $("button[name='save']").prop("disabled", false);
    }
}

$("#isbn, #vendor, #title").on("keyup change", checkDuplicate); // also check when title changes

</script>
<script>
(() => {
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



    
 


<?php include'footer.php' ?>

