<?php
include 'dbb.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $username  = mysqli_real_escape_string($conn, $_POST['username']);
    $password  = mysqli_real_escape_string($conn, $_POST['password']);
    $email     = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile    = mysqli_real_escape_string($conn, $_POST['mobile']);

    $user_type = 0;
    $photo = '';
    $status = 0;
    $account_name = $full_name;
    $hashed_password = $password; // Consider hashing with password_hash()

    $check = "SELECT * FROM admin WHERE username='$username' OR email='$email'";
    $result = mysqli_query($conn, $check);

    if (mysqli_num_rows($result) > 0) {
        echo "<script>alert('❌ Username or Email already exists!'); window.history.back();</script>";
        exit;
    }

    $sql = "INSERT INTO admin 
        (account_name, status, address, city, shift, phone, mobile, email, username, password, user_type, photo, nic, last_login)
        VALUES 
        ('$account_name', '$status', '', '', NULL, '', '$mobile', '$email', '$username', '$hashed_password', '$user_type', '$photo', '', NULL)";

    if (mysqli_query($conn, $sql)) {
        echo "<script>alert('✅ User saved successfully!'); window.location.href='user_for.php';</script>";
    } else {
        echo "<script>alert('❌ Error: " . mysqli_error($conn) . "'); window.history.back();</script>";
    }
}
?>
