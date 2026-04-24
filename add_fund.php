<?php
include "dbb.php";

if (isset($_POST['save'])) {
  $title = $_POST['title']; // text from input
  $price = $_POST['price'];
  $date  = $_POST['date'];

  $insert = $conn->prepare("INSERT INTO funds (title, price, date) VALUES (?, ?, ?)");
  $insert->bind_param("sds", $title, $price, $date);

  if ($insert->execute()) {
    header("Location: funds.php");
    exit;
  } else {
    echo "Error: " . $insert->error;
  }
}

require "topheader.php";
?>

<div class="pagetitle text-center">
  <h1>Add New Fund</h1>
</div>

<section class="section">
  <div class="row justify-content-center">
    <div class="col-lg-6 col-md-8 col-sm-10">
      <div class="card shadow">
        <div class="card-body">
          <h5 class="card-title text-center">Fund Details</h5>

          <form method="POST" autocomplete="off">
           <div class="mb-3">
  <label class="form-label fw-semibold">Fund Title</label>
  <select class="form-select" name="title" id="title" required>
    <option value="" selected disabled>Select Fund Title</option>
    <?php
    $sql = "SELECT id, title FROM fund_titles ORDER BY title ASC";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<option value='{$row['id']}'>{$row['title']}</option>";
        }
    } else {
        echo "<option disabled>No Fund Titles Found</option>";
    }
    ?>
  </select>
</div>

            <div class="mb-3">
              <label class="form-label">Price</label>
              <input type="number" step="0.01" name="price" class="form-control" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Date</label>
              <input type="date" name="date" class="form-control" required>
            </div>

            <div class="text-center">
              <button type="submit" name="save" class="btn" style="background-color: #045E70; color:white;">Save</button>
              <a href="funds.php" class="btn btn-secondary">Cancel</a>
            </div>
          </form>

        </div>
      </div>
    </div>
  </div>
</section>

<!-- 🧩 AJAX Script -->
<!-- <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function(){
  $('#title').on('keyup', function(){
    let query = $(this).val().trim();
    if (query.length > 0) {
      $.ajax({
        url: 'fetch_titles.php', // path must be correct
        method: 'POST',
        data: { query: query },
        success: function(data){
          $('#suggestions').html(data).fadeIn();
        },
        error: function(xhr, status, error){
          console.error("AJAX Error:", status, error);
        }
      });
    } else {
      $('#suggestions').fadeOut();
    }
  });

  // When a suggestion is clicked
  $(document).on('click', '.suggestion-item', function(e){
    e.preventDefault();
    $('#title').val($(this).text());
    $('#suggestions').fadeOut();
  });
});
</script> -->


<?php require "footer.php"; ?>
