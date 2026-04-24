<?php
include "dbb.php";

$result = $conn->query("SELECT * FROM author");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Author List</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">

<?php if (isset($_GET['saved']) && $_GET['saved'] == 1): ?>
  <div class="alert alert-success" role="alert">
    Author Saved Successfully!
  </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Authors</h3>
  <a href="register.php" class="btn btn-primary">+ Add New Author</a>
</div>

<table class="table table-bordered table-striped">
  <thead class="table-dark">
    <tr>
      <th>#</th>
      <th>Name</th>
    </tr>
  </thead>
  <tbody>
    <?php 
    $count = 1;
    while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?= $count++ ?></td>
        <td><?= $row['author_name'] ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

</body>
</html>
