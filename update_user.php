<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'dbb.php';

if (isset($_POST['staff_id'])) {
    $id       = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $name     = mysqli_real_escape_string($conn, $_POST['account_name']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $mobile   = mysqli_real_escape_string($conn, $_POST['mobile']);
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // ✅ Update query (no password hashing)
    if (!empty($password)) {
        $query = "
            UPDATE admin 
            SET 
                account_name = '$name',
                username = '$username',
                email = '$email',
                mobile = '$mobile',
                password = '$password'
            WHERE staff_id = '$id'
        ";
    } else {
        // keep old password
        $query = "
            UPDATE admin 
            SET 
                account_name = '$name',
                username = '$username',
                email = '$email',
                mobile = '$mobile'
            WHERE staff_id = '$id'
        ";
    }

    if (mysqli_query($conn, $query)) {
        echo "<script>
            alert('User updated successfully!');
            window.location='user_for.php';
        </script>";
    } else {
        echo "<script>
            alert('Error updating user: " . mysqli_error($conn) . "');
            window.location='user_for.php';
        </script>";
    }
} else {
    echo "<script>
        alert('Invalid request.');
        window.location='user_management.php';
    </script>";
}
?>
