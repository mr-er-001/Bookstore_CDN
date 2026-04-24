<?php
include "dbb.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $publisher_name = trim($_POST['publisher_name']); // Clean input

    // 🟡 Validate empty field
    if (empty($publisher_name)) {
        echo "<script>alert('Publisher name cannot be empty!'); window.history.back();</script>";
        exit();
    }

    // 🔍 Check for duplicate (case-insensitive)
    $check_sql = "SELECT id FROM publisher WHERE LOWER(publisher_name) = LOWER(?)";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $publisher_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // ❌ Publisher already exists
        echo "<script>alert('This publisher name already exists!'); window.history.back();</script>";
        exit();
    }

    // ✅ Insert new publisher
    $insert_sql = "INSERT INTO publisher (publisher_name) VALUES (?)";
    $stmt = $conn->prepare($insert_sql);
    $stmt->bind_param("s", $publisher_name);

    if ($stmt->execute()) {
        header("Location: pub_data.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
