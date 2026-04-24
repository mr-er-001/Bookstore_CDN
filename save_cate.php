<?php
include "dbb.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_name = trim($_POST['category_name']); // Remove extra spaces

    // 🟡 Validate input
    if (empty($category_name)) {
        echo "<script>alert('Category name cannot be empty!'); window.history.back();</script>";
        exit();
    }

    // 🔍 Check for duplicate category (case-insensitive)
    $check_sql = "SELECT id FROM category WHERE LOWER(category_name) = LOWER(?)";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $category_name);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows > 0) {
        // ❌ Category already exists
        echo "<script>alert('This category name already exists!'); window.history.back();</script>";
        exit();
    }

    // ✅ Insert new category
    $sql = "INSERT INTO category (category_name) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $category_name);

    if ($stmt->execute()) {
        header("Location: cate_data.php?success=1");
        exit();
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
