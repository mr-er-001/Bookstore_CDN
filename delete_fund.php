<?php
include "dbb.php";

$id = $_GET['id'] ?? 0;

if($id > 0) {
  mysqli_query($conn, "DELETE FROM funds WHERE id='$id'");
}

header("Location: funds.php");
exit;
