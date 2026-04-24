<?php
$host = "localhost";
$user = "allrounder";   // default XAMPP MySQL user
$pass = "7ujm&5tgb%";       // default is empty in XAMPP
$db   = "db_bookstore_old"; // <-- your actual database name

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("❌ Connection failed: " . mysqli_connect_error());
}
?>
