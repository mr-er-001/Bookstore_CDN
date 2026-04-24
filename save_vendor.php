<?php
include "dbb.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 🧹 Sanitize inputs
    $company_name = trim($_POST['company_name']);
    $username     = trim($_POST['username']);
    $email        = trim($_POST['email']);
    $phone        = trim($_POST['phone']);
    $mobile       = trim($_POST['mobile']);
    $address      = trim($_POST['address']);

    // 🟡 Basic validation
    if (empty($company_name) || empty($username) || empty($email)) {
        echo "<script>alert('Company Name, Contact Name, and Email are required!'); window.history.back();</script>";
        exit();
    }

    // 🔍 Check for duplicate company name
    $check_company = "SELECT id FROM vendor WHERE LOWER(company_name) = LOWER(?)";
    $stmt = $conn->prepare($check_company);
    $stmt->bind_param("s", $company_name);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "<script>alert('This Vendor Name already exists!'); window.history.back();</script>";
        exit();
    }

    // 🔍 Check for duplicate contact name
    $check_contact = "SELECT id FROM vendor WHERE LOWER(contact_name) = LOWER(?)";
    $stmt = $conn->prepare($check_contact);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "<script>alert('This Contact Name already exists!'); window.history.back();</script>";
        exit();
    }

    // 🔍 Check for duplicate email
    $check_email = "SELECT id FROM vendor WHERE LOWER(email) = LOWER(?)";
    $stmt = $conn->prepare($check_email);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        echo "<script>alert('This Email already exists!'); window.history.back();</script>";
        exit();
    }

    // ✅ Insert new vendor if no duplicates
    $sql = "INSERT INTO vendor (company_name, contact_name, email, phone, mobile, postal_address) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $company_name, $username, $email, $phone, $mobile, $address);

    if ($stmt->execute()) {
        header("Location: vendor_data.php?success=1");
        exit();
    } else {
        echo "Database Error: " . $stmt->error;
    }

    $stmt->close();
}
$conn->close();
?>
