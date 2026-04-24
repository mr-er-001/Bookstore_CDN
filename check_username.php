<?php
include 'dbb.php'; // Database connection

// Check if username is provided
if (isset($_POST['username'])) {
  $username = $conn->real_escape_string($_POST['username']);

  // Query to check if the username exists in the database
  $sql = "SELECT id FROM admin WHERE username = '$username'";
  $result = $conn->query($sql);

  // If username exists, return 'exists', else return 'available'
  if ($result->num_rows > 0) {
    echo 'exists';  // Username already exists
  } else {
    echo 'available';  // Username is available
  }
}

$conn->close();
?>