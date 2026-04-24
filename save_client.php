<?php
include "dbb.php"; // database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $company_name   = mysqli_real_escape_string($conn, $_POST['company_name']);
    $contact_name   = mysqli_real_escape_string($conn, $_POST['contact_name']);
    $postal_address = mysqli_real_escape_string($conn, $_POST['postal_address']);
    $phone          = mysqli_real_escape_string($conn, $_POST['phone']);
    $mobile         = mysqli_real_escape_string($conn, $_POST['mobile']);
    $email          = mysqli_real_escape_string($conn, $_POST['email']);

    // 🧩 Check if company name already exists
    $check_company = "SELECT * FROM client WHERE company_name = '$company_name'";
    $result_company = mysqli_query($conn, $check_company);

    if (mysqli_num_rows($result_company) > 0) {
        echo "<script>
            alert('⚠️ This company name already exists. Please use a different name.');
            window.history.back();
        </script>";
        exit;
    }

    // 🧩 Check if email already exists
    $check_email = "SELECT * FROM client WHERE email = '$email'";
    $result_email = mysqli_query($conn, $check_email);

    if (mysqli_num_rows($result_email) > 0) {
        echo "<script>
            alert('⚠️ This email address is already registered. Please use another email.');
            window.history.back();
        </script>";
        exit;
    }

    // ✅ If both are unique, insert the new record
    $sql = "INSERT INTO client (company_name, contact_name, postal_address, phone, mobile, email, status, total_amount) 
            VALUES ('$company_name', '$contact_name', '$postal_address', '$phone', '$mobile', '$email', '1', '0')";

    if (mysqli_query($conn, $sql)) {
        echo "<script>
            alert('✅ Client registered successfully!');
            window.location.href = 'client_data.php';
        </script>";
        exit;
    } else {
        echo "<script>
            alert('❌ Error saving client: " . mysqli_error($conn) . "');
            window.history.back();
        </script>";
    }
}

mysqli_close($conn);
?>
