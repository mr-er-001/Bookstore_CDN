<?php
include "dbb.php";

$response = ['exists' => false, 'field' => ''];

if (isset($_POST['company_name'])) {
    $company_name = mysqli_real_escape_string($conn, $_POST['company_name']);
    $sql = "SELECT id FROM client WHERE company_name = '$company_name'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $response = ['exists' => true, 'field' => 'company_name'];
    }
}

if (isset($_POST['email'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $sql = "SELECT id FROM client WHERE email = '$email'";
    $result = mysqli_query($conn, $sql);
    if (mysqli_num_rows($result) > 0) {
        $response = ['exists' => true, 'field' => 'email'];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
